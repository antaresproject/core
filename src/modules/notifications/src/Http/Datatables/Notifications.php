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

namespace Antares\Notifications\Http\Datatables;

use Antares\Notifications\Model\Notifications as NotificationsModel;
use Antares\Notifications\Model\NotificationCategory;
use Antares\Notifications\Filter\NotificationFilter;
use Antares\Notifications\Model\NotificationTypes;
use Antares\Datatables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Notifications extends DataTable
{

    /**
     * Available filters
     *
     * @var array 
     */
    protected $filters = [
        NotificationFilter::class
    ];

    /**
     * items per page
     *
     * @var mixed 
     */
    public $perPage = 25;

    /**
     * @return Builder
     */
    public function query()
    {
        return app(NotificationsModel::class)->select(['tbl_notifications.*'])->with(['category', 'contents'])->whereHas('contents', function ($query) {
                    $query->where('lang_id', lang_id());
                });
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $acl             = app('antares.acl')->make('antares/notifications');
        $canUpdate       = $acl->can('notifications-edit');
        $canTest         = $acl->can('notifications-test');
        $canChangeStatus = $acl->can('notifications-change-status');
        $canDelete       = $acl->can('notifications-delete');
        return $this->prepare()
                        ->filter(function ($query) {
                            $request = app('request');
                            $keyword = array_get($request->get('search'), 'value');
                            if (is_null($keyword) or is_null($keyword)) {
                                return;
                            }

                            $query
                            ->leftJoin('tbl_notification_categories', 'tbl_notifications.category_id', '=', 'tbl_notification_categories.id')
                            ->leftJoin('tbl_notification_contents', 'tbl_notifications.id', '=', 'tbl_notification_contents.notification_id');
                            $columns    = request()->get('columns');
                            $typeId     = null;
                            $categoryId = null;
                            array_walk($columns, function($item, $index) use(&$typeId, &$categoryId) {
                                if (array_get($item, 'data') == 'type') {
                                    $typeId = array_get($item, 'search.value');
                                }
                                if (array_get($item, 'data') == 'category') {
                                    $categoryId = array_get($item, 'search.value');
                                }
                                return false;
                            });
                            if (!$categoryId) {
                                $categoryId = NotificationCategory::where('name', 'default')->first()->id;
                            }
                            $query->where('category_id', $categoryId);
                            if (!$typeId) {
                                $typeId = NotificationTypes::where('name', 'email')->first()->id;
                            }
                            $query->where('type_id', $typeId);

                            if ($keyword !== '') {
                                $query->whereRaw("(tbl_notification_contents.title like '%$keyword%' or tbl_notification_categories.title like '%$keyword%')");
                            }
                            $query->groupBy('tbl_notification_contents.notification_id');
                        })
                        ->filterColumn('type', function($query, $keyword) {
                            $query->where('type_id', $keyword);
                        })
                        ->filterColumn('category', function($query, $keyword) {
                            $columns = request()->get('columns');
                            $search  = null;
                            array_walk($columns, function($item, $index) use(&$search) {
                                if (array_get($item, 'data') == 'type') {
                                    $search = array_get($item, 'search.value');
                                    return;
                                }
                                return false;
                            });
                            if (!$search) {
                                $typeId = NotificationTypes::where('name', 'email')->first()->id;
                                $query->where('type_id', $typeId);
                            }
                            $query->where('category_id', $keyword);
                        })
                        ->editColumn('category', function ($model) {
                            return $model->category->title;
                        })
                        ->editColumn('type', function ($model) {
                            return $model->type->title;
                        })
                        ->editColumn('title', function ($model) {
                            $first = $model->contents->first();
                            return !is_null($first) ? $first->title : '';
                        })
                        ->editColumn('active', function ($model) {
                            return ((int) $model->active) ?
                                    '<span class="label-basic label-basic--success">' . trans('Yes') . '</span>' :
                                    '<span class="label-basic label-basic--danger">' . trans('No') . '</span>';
                        })
                        ->addColumn('action', $this->getActionsColumn($canUpdate, $canTest, $canChangeStatus, $canDelete))
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        publish('notifications', ['js/notifications-table.js']);
        return $this->setName('Notifications List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'data' => 'id', 'title' => 'Id'])
                        ->addColumn(['data' => 'title', 'name' => 'title', 'title' => trans('antares/notifications::messages.title'), 'className' => 'bolded'])
                        ->addColumn(['data' => 'event', 'name' => 'event', 'title' => trans('Event')])
                        ->addColumn(['data' => 'category', 'name' => 'category', 'title' => trans('Category')])
                        ->addColumn(['data' => 'type', 'name' => 'type', 'title' => trans('Type')])
                        ->addColumn(['data' => 'active', 'name' => 'active', 'title' => trans('Enabled')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->setDeferedData()
                        ->addGroupSelect($this->categories(), 3, null, ['data-prefix' => trans('antares/notifications::messages.datatables.select_category'), 'class' => 'mr24', 'id' => 'datatables-notification-category'])
                        ->addGroupSelect($this->types(), 4, null, ['data-prefix' => trans('antares/notifications::messages.datatables.select_type'), 'class' => 'mr24', 'id' => 'datatables-notification-type']);
    }

    /**
     * Creates select for categories
     * 
     * @return Collection
     */
    protected function categories(): Collection
    {
        return NotificationCategory::pluck('title', 'id');
    }

    /**
     * Creates select for types
     * 
     * @return Collection
     */
    protected function types(): Collection
    {
        return NotificationTypes::pluck('title', 'id');
    }

    /**
     * Get actions column for table builder.
     * @return callable
     */
    protected function getActionsColumn($canUpdate, $canTest, $canChangeStatus, $canDelete)
    {
        return function ($row) use($canUpdate, $canTest, $canChangeStatus, $canDelete) {
            $btns = [];
            $html = app('html');
            if ($canUpdate) {
                $btns[] = $html->create('li', $html->link(handles("antares::notifications/edit/" . $row->id), trans('Edit'), ['data-icon' => 'edit']));
            }

            if ($canChangeStatus) {
                $btns[] = $html->create('li', $html->link(handles("antares::notifications/changeStatus/" . $row->id), $row->active ? trans('Disable') : trans('Enable'), ['class' => "triggerable confirm", 'data-icon' => $row->active ? 'minus-circle' : 'check-circle', 'data-title' => trans("Are you sure?"), 'data-description' => trans('Changing status of notification') . ' #' . $row->contents[0]->title]));
            }
            if ($canTest && in_array($row->type->name, ['email', 'sms'])) {
                $btns[] = $html->create('li', $html->link(handles("antares::notifications/sendtest/" . $row->id), trans('Send preview'), ['class' => "triggerable confirm", 'data-icon' => 'desktop-windows', 'data-title' => trans("Are you sure?"), 'data-description' => trans('Sending preview notification with item') . ' #' . $row->contents[0]->title]));
            }

            if ($canDelete and ( ( $row->event == config('antares/notifications::default.custom_event')))) {
                $btns[] = $html->create('li', $html->link(handles("antares::notifications/delete/" . $row->id), trans('Delete'), ['class' => "triggerable confirm", 'data-icon' => 'delete', 'data-title' => trans("Are you sure?"), 'data-description' => trans('Deleting item #') . ' #' . $row->id]));
            }
            if (empty($btns)) {
                return '';
            }

            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();

            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}
