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

namespace Antares\Notifications\Repository;

use Antares\Notifications\Model\NotificationContents;
use Antares\Foundation\Repository\AbstractRepository;
use Antares\Notifications\Model\NotificationSeverity;
use Antares\Notifications\Model\NotificationTypes;
use Antares\Notifications\Model\Notifications;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class Repository extends AbstractRepository
{

    /**
     * name of repositroy model
     * 
     * @return Notifications
     */
    public function model()
    {
        return Notifications::class;
    }

    /**
     * find models by level type
     * 
     * @param String $type
     * @return Collection
     */
    public function findByLevelType($type)
    {
        return $this->model->with(['category', 'type']);
    }

    /**
     * find model by locale
     * 
     * @param mixed $id
     * @param String $locale
     * @return Model
     */
    public function findByLocale($id, $locale)
    {
        return $this->model->with(['type', 'contents' => function($query) {
                        $query->with('lang');
                    }])->where('id', $id)->first();
    }

    /**
     * find model with contents relation
     * 
     * @param mixed $id
     * @return Model
     */
    public function findWithContents($id)
    {
        return $this->model->where('id', $id)->with(['contents'])->first();
    }

    /**
     * Updates notification
     * 
     * @param mixes $id
     * @param array $data
     */
    public function updateNotification($id, array $data)
    {
        DB:: beginTransaction();
        try {
            $model         = $this->model->find($id);
            $model->active = array_get($data, 'active', 0);
            $model->save();
            $titles        = array_get($data, 'title', []);
            foreach ($titles as $langId => $title) {
                $content          = $model->contents()->getModel()->firstOrNew(['notification_id' => $id, 'lang_id' => $langId]);
                $content->content = $data['content'][$langId];
                $content->title   = $title;
                $model->contents()->save($content);
            }
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }
        DB::commit();
        return true;
    }

    /**
     * stores new notification
     * 
     * @param array $data
     */
    public function store(array $data)
    {
        DB::transaction(function() use($data) {
            $model = $this->storeNotificationInstance($data);
            foreach ($data['title'] as $langId => $content) {
                $model->contents()->save($model->contents()->getModel()->newInstance([
                            'notification_id' => $model->id,
                            'lang_id'         => $langId,
                            'title'           => $content,
                            'content'         => $data['content'][$langId]
                ]));
            }
        });
    }

    /**
     * Stores notification instance details
     * 
     * @param array $data
     * @return Model
     */
    protected function storeNotificationInstance(array $data)
    {

        is_null($typeId = array_get($data, 'type_id')) ? $typeId = app(NotificationTypes::class)->where('name', array_get($data, 'type'))->first()->id : null;
        $model  = $this->model->getModel()->newInstance([
            'category_id' => array_get($data, 'category'),
            'type_id'     => $typeId,
            'active'      => array_get($data, 'active', 1),
            'severity_id' => NotificationSeverity::medium()->first()->id,
            'event'       => config('antares/notifications::default.custom_event')
        ]);
        $model->save();
        return $model;
    }

    /**
     * Stores system notifications
     * 
     * @param array $data
     * @param array $areas
     * @throws Exception
     */
    public function sync(array $data, array $areas = [])
    {
        DB::beginTransaction();
        try {
            $templates = array_get($data, 'templates', []);
            $model     = $this->storeNotificationInstance($data);
            $langs     = array_get($data, 'languages', []);
            foreach ($langs as $lang) {
                $this->processSingle($data, $lang, $model);
            }
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return true;
    }

    /**
     * Process signle notification
     * 
     * @param array $data
     * @param \Antares\Translations\Models\Languages $lang
     * @param Notifications $model
     * @return boolean
     * @throws Exception
     */
    protected function processSingle(array $data, $lang, $model)
    {
        is_null($view    = array_get($data, 'templates.' . $lang->code)) ? $view    = array_get($data, 'templates.' . $lang->code) : null;
        $content = '';
        if (is_null($view)) {
            $content = app($data['classname'])->render();
        } else {
            $path = view($view)->getPath();
            if (!file_exists($path)) {
                throw new Exception(trans('antares/notifications::messages.notification_view_not_exists', ['path' => $path]));
            }
            $content = file_get_contents($path);
        }

        return $model->contents()->save($model->contents()->getModel()->newInstance([
                            'notification_id' => $model->id,
                            'lang_id'         => $lang->id,
                            'title'           => array_get($data, 'title'),
                            'content'         => $content
        ]));
    }

    /**
     * Finds sendable notifications
     * 
     * @return \Illuminate\Database\Query\Builder
     */
    public function findSendable()
    {
        return $this->makeModel()->with('contents')->get();
    }

    /**
     * Gets notification contents
     * 
     * @param String $type
     * @return \Antares\Support\Collection
     */
    public function getNotificationContents($type = null)
    {
        return NotificationContents::select(['title', 'id'])
                        ->whereHas('lang', function($query) {
                            $query->where('code', locale());
                        })
                        ->whereHas('notification', function($query) use($type) {
                            if (is_null($type)) {
                                $query->whereHas('type', function($subquery) {
                                    $subquery->whereIn('name', ['sms', 'email']);
                                });
                            } elseif (is_numeric($type)) {
                                $query->where('type_id', $type);
                            } elseif (is_string($type)) {
                                $query->whereHas('type', function($subquery) use($type) {
                                    $subquery->where('name', $type);
                                });
                            }
                        })->get();
    }

    /**
     * Gets decorated notification types
     * 
     * @return \Illuminate\Database\Query\Builder
     */
    public function getDecoratedNotificationTypes()
    {
        return NotificationTypes::whereIn('name', ['email', 'sms'])->pluck('name', 'id')->map(function ($item, $key) {
                    return ucfirst($item);
                });
    }

}
