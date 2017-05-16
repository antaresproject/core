<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
declare(strict_types = 1);

namespace Antares\Notifications\Repository;

use Antares\Notifications\Model\NotificationsStackRead;
use Antares\Notifications\Model\NotificationSeverity;
use Antares\Foundation\Repository\AbstractRepository;
use Antares\Notifications\Model\NotificationCategory;
use Antares\Notifications\Model\NotificationContents;
use Antares\Notifications\Model\NotificationsStack;
use Antares\Notifications\Model\NotificationTypes;
use Antares\Notifications\Model\Notifications;
use Illuminate\Database\Eloquent\Builder;
use Antares\Widgets\Model\Widgets;
use Illuminate\Support\Facades\DB;
use Antares\Logger\Model\Logs;
use Exception;

class StackRepository extends AbstractRepository
{

    /**
     * name of repositroy model
     *
     * @return Widgets
     */
    public function model()
    {
        return NotificationsStack::class;
    }

    /**
     * push notification to database
     * 
     * @param String $type
     * @param String $name
     * @param array $value
     * @return boolean
     */
    public function push($type, $name, $value)
    {
        $typeModel = NotificationTypes::where('name', $type)->first();
        if (is_null($typeModel)) {
            return false;
        }
        $tId   = $typeModel->id;
        $model = $this->makeModel()->getModel()->newInstance([
            'type_id' => $tId,
            'name'    => $name,
            'value'   => $value
        ]);
        $model->save();
        return $model;
    }

    /**
     * finds all new notification messages
     * 
     * @param array $ids
     * @return \Illuminate\Database\Query\Builder
     */
    public function findAllNew($ids = [])
    {
        $query = $model = $this->makeModel()->where('broadcasted', 0);
        if (!empty($ids)) {
            $values = array_values($ids);
            $query->whereNotIn('id', $values);
        }
        return $query->get();
    }

    /**
     * Gets notifications severity ids
     * 
     * @return array
     */
    protected function getNotificationsSeverityIds()
    {
        return NotificationSeverity::whereIn('name', config('antares/notifications::notification_severity'))->get()->pluck('id')->toArray();
    }

    /**
     * Gets alerts severity ids
     * 
     * @return array
     */
    protected function getAlertsSeverityIds()
    {
        return NotificationSeverity::whereIn('name', config('antares/notifications::alert_severity'))->get()->pluck('id')->toArray();
    }

    /**
     * Gets base query builder for notifications and alerts
     * 
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        $read = NotificationsStackRead::select(['stack_id'])
                ->withTrashed()
                ->where('user_id', user()->id)
                ->whereNotNull('deleted_at')
                ->pluck('stack_id');
        return $this->makeModel()->newQuery()
                        ->distinct()
                        ->select(['tbl_notifications_stack.*'])
                        ->whereHas('content', function($query) {
                            $query->where([
                                'lang_id' => lang_id()
                            ]);
                        })
                        ->whereHas('notification', function($query) {
                            $query->whereHas('type', function($subquery) {
                                $subquery->where('name', area());
                            });
                        })
                        ->where(function ($query) {
                            $query
                            ->whereNull('author_id')
                            ->orWhere('author_id', user()->id)
                            ->orWhereHas('author', function($subquery) {
                                $subquery->whereHas('roles', function($rolesQuery) {
                                    $rolesQuery->whereIn('tbl_roles.id', user()->roles->first()->getChilds());
                                });
                            })
                            ->orWhereHas('params', function($subquery) {
                                $subquery->where('model_id', user()->id);
                            });
                        })
                        ->whereNotIn('id', $read)
                        ->with('author')
                        ->with('content')->with('notification.severity')
                        ->orderBy('tbl_notifications_stack.created_at', 'desc');
    }

    /**
     * Gets user notifications
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNotifications()
    {
        return $this->query()->whereHas('notification', function($query) {
                    $query->whereIn('tbl_notifications.severity_id', $this->getNotificationsSeverityIds());
                });
    }

    /**
     * Alerts getter
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAlerts()
    {
        return $this->query()->whereHas('notification', function($query) {
                    $query->whereIn('tbl_notifications.severity_id', $this->getAlertsSeverityIds());
                });
    }

    /**
     * Assign counter query
     * 
     * @param \Illuminate\Database\Query\Builder $builder
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function count($builder)
    {
        $read = NotificationsStackRead::select(['stack_id'])->withTrashed()->where('user_id', user()->id)->pluck('stack_id');
        return $builder->whereNotIn('id', $read)->count();
    }

    /**
     * Gets notifications and alerts count
     * 
     * @return array
     */
    public function getCount()
    {
        return [
            'notifications' => $this->count($this->getNotifications()),
            'alerts'        => $this->count($this->getAlerts())
        ];
    }

    /**
     * Deletes all messages
     * 
     * @param String $type
     * @return boolean
     */
    public function clear($type = 'notifications')
    {
        $builder = ($type == 'alerts') ? $this->getAlerts() : $this->getNotifications();

        return $this->makeModel()
                        ->getModel()
                        ->read()
                        ->getModel()
                        ->newQuery()
                        ->whereIn('stack_id', $builder->pluck('id'))->delete();
    }

    /**
     * Mark notifications or alerts as read
     * 
     * @param String $type
     * @return boolean
     */
    public function markAsRead($type = 'notifications')
    {
        DB::beginTransaction();
        try {
            $builder = ($type == 'alerts') ? $this->getAlerts() : $this->getNotifications();
            $read    = NotificationsStackRead::select(['stack_id'])->withTrashed()->where('user_id', user()->id)->pluck('stack_id');
            $items   = $builder->whereNotIn('id', $read)->get();

            foreach ($items as $item) {
                $item->read()->save(new NotificationsStackRead([
                    'user_id' => user()->id
                ]));
                $item->save();
            }
        } catch (Exception $ex) {
            DB::rollback();
            return false;
        }
        return DB::commit();
    }

    /**
     * Deletes item by id
     * 
     * @param mixed $id
     * @return boolean
     */
    public function deleteById($id)
    {
        $read = NotificationsStackRead::where([
                    'stack_id' => $id,
                    'user_id'  => user()->id
                ])->first();
        if (!is_null($read)) {
            return $read->delete();
        }
        return false;
    }

    /**
     * Saves notification
     * 
     * @param array $log
     * @param String $message
     * @return boolean
     * @throws Exception
     */
    public function save(array $log, $message = null)
    {
        DB::beginTransaction();
        try {
            $this->resolveJsonCastableColumns($log);
            $inserted = Logs::insert($log);
            if (!$inserted) {
                throw new Exception('Unable to save log');
            }
            $lid          = DB::getPdo()->lastInsertId();
            $notification = Notifications::query()->firstOrNew([
                'brand_id'    => brand_id(),
                'category_id' => NotificationCategory::where('name', 'default')->first()->id,
                'type_id'     => NotificationTypes::where('name', 'admin')->first()->id,
                'name'        => $log['name'],
            ]);
            if (!$notification->exists) {
                $notification->active = 1;
                $notification->save();
                $notification->contents()->save(new NotificationContents([
                    'lang_id' => lang_id(),
                    'title'   => $log['name'],
                    'content' => $message
                ]));
            }
        } catch (Exception $ex) {
            DB::rollback();
            return false;
        }
        DB::commit();
        return true;
    }

    /**
     * Resolves json castable columns
     * 
     * @return array
     */
    protected function resolveJsonCastableColumns(&$log)
    {
        foreach ($log as $name => $value) {
            if (!is_array($value)) {
                continue;
            }
            $log[$name] = json_encode($value);
        }
        return $log;
    }

    /**
     * Fetch stacks
     * 
     * @param array $columns
     * @return Builder
     */
    public function fetchAll(array $columns = []): Builder
    {
        return $this->makeModel()
                        ->newQuery()
                        ->select([
                            'tbl_notifications_stack.id',
                            'tbl_notifications_stack.variables as variables',
                            'tbl_notifications_stack.created_at as created_at',
                            'tbl_notifications_stack.author_id as author_id',
                            'tbl_notifications.event as name',
                            'tbl_notification_contents.title as title',
                            'tbl_notification_types.title as type',
                            'tbl_languages.code as lang_code',
                            'tbl_languages.name as lang_name',
                            'tbl_roles.area as area',
                            DB::raw('CONCAT_WS(" ",tbl_users.firstname,tbl_users.lastname) AS fullname')
                        ])
                        ->leftJoin('tbl_notification_contents', 'tbl_notifications_stack.notification_id', '=', 'tbl_notification_contents.notification_id')
                        ->leftJoin('tbl_notifications', 'tbl_notifications_stack.notification_id', '=', 'tbl_notifications.id')
                        ->leftJoin('tbl_notification_types', 'tbl_notifications.type_id', '=', 'tbl_notification_types.id')
                        ->leftJoin('tbl_languages', 'tbl_notification_contents.lang_id', '=', 'tbl_languages.id')
                        ->leftJoin('tbl_users', 'tbl_notifications_stack.author_id', '=', 'tbl_users.id')
                        ->leftJoin('tbl_user_role', 'tbl_users.id', '=', 'tbl_user_role.user_id')
                        ->leftJoin('tbl_roles', 'tbl_user_role.role_id', '=', 'tbl_roles.id')
                        ->where(function($query) {
                            $query->whereNull('author_id')
                            ->orWhere('author_id', user()->id)
                            ->orWhereIn('tbl_roles.id', user()->roles->first()->getChilds());
                        });
    }

    /**
     * Fetch one stack item
     * 
     * @param int $id
     * @return Builder
     */
    public function fetchOne(int $id): Builder
    {
        return $this->makeModel()->newQuery()->withoutGlobalScopes()->distinct()
                        ->select(['tbl_notifications_stack.*'])->with('content')->with('content.lang')->with('notification.type')
                        ->where(function ($query) {
                            $query->whereNull('author_id')->orWhere('author_id', user()->id)->orWhereHas('author', function($subquery) {
                                $subquery->whereHas('roles', function($rolesQuery) {
                                    $rolesQuery->whereIn('tbl_roles.id', user()->roles->first()->getChilds());
                                });
                            });
                        })
                        ->with('author')->with('author.roles')->with('content')
                        ->whereId($id);
    }

}
