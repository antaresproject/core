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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
declare(strict_types = 1);

namespace Antares\Notifications\Http\Datatables;

use Antares\Notifications\Model\NotificationTypes;
use Antares\Datatables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;
use Antares\Support\Facades\Form;
use Antares\Notifications\Filter\DateRangeNotificationLogsFilter;
use Antares\Notifications\Filter\NotificationNameFilter;
use Antares\Notifications\Filter\NotificationLangFilter;
use Antares\Notifications\Filter\NotificationAreaFilter;
use Antares\Notifications\Repository\StackRepository;

class Logs extends DataTable
{

    /**
     * Available filters
     *
     * @var array 
     */
    protected $filters = [
        DateRangeNotificationLogsFilter::class,
        NotificationNameFilter::class,
        NotificationLangFilter::class,
        NotificationAreaFilter::class
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
        return app(StackRepository::class)->fetchAll();
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        return $this->prepare()
                        ->editColumn('lang_code', function ($row = null) {
                            $code     = $row->lang_code;
                            $codeIcon = (($code == 'en') ? 'us' : $code);
                            return '<i data-tooltip-inline="' . $row->lang_name . '" class="flag-icon flag-icon-' . $codeIcon . '"></i>';
                        })
                        ->editColumn('area', function ($row = null) {
                            $area       = !is_null($recipients = array_get($row->variables, 'recipients')) ? user($recipients[0]['id'])->getArea() : $row->area;
                            return config('areas.areas.' . $area);
                        })
                        ->editColumn('fullname', function ($row = null) {
                            $recipients = !is_null($recipients = array_get($row->variables, 'recipients')) ? $recipients[0] : [];
                            $id         = !empty($recipients) ? array_get($recipients, 'id') : $row->author_id;

                            $title = '#' . $id . ' ' . array_get($recipients, 'fullname', $row->fullname);
                            return app('html')->link(handles('antares/foundation::users/' . $id), $title)->get();
                        })
                        ->addColumn('action', $this->getActionsColumn())
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        publish('notifications', ['js/notification-logs.js']);
        $html = app('html');
        return $this->setName('Notifications List')
                        ->addColumn(['data' => 'tbl_notifications_stack.id', 'name' => 'tbl_notifications_stack.id', 'data' => 'id', 'title' => 'Id'])
                        ->addColumn(['data' => 'created_at', 'name' => 'tbl_notifications_stack.created_at', 'title' => trans('antares/notifications::logs.headers.date'), 'className' => 'bolded'])
                        ->addColumn(['data' => 'name', 'name' => 'tbl_notifications.event', 'title' => trans('antares/notifications::logs.headers.name')])
                        ->addColumn(['data' => 'lang_code', 'name' => 'tbl_languages.code', 'title' => trans('antares/notifications::logs.headers.lang')])
                        ->addColumn(['data' => 'title', 'name' => 'tbl_notification_contents.title', 'title' => trans('antares/notifications::logs.headers.title')])
                        ->addColumn(['data' => 'type', 'name' => 'tbl_notification_types.title', 'title' => trans('antares/notifications::logs.headers.type')])
                        ->addColumn(['data' => 'area', 'name' => 'area', 'title' => trans('antares/notifications::logs.headers.level')])
                        ->addColumn(['data' => 'fullname', 'name' => 'tbl_users.firstname', 'title' => trans('antares/notifications::logs.headers.user')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->addGroupSelect($this->types(), 5, null, ['data-prefix' => trans('antares/notifications::messages.datatables.select_type')])
                        ->addMassAction('delete', $html->link(handles('antares::notifications/logs/delete', ['csrf' => true]), $html->raw('<i class="zmdi zmdi-delete"></i><span>' . trans('antares/notifications::logs.actions.delete') . '</span>'), [
                                    'class'            => "triggerable confirm mass-action",
                                    'data-title'       => trans("antares/notifications::logs.are_you_sure"),
                                    'data-description' => trans("antares/notifications::logs.mass_deleteing_notification_logs_desc"),
                        ]))->parameters([
                    'order'        => [[1, 'desc']],
                    'aoColumnDefs' => [
                        ['width' => '1%', 'targets' => 0],
                        ['width' => '7%', 'targets' => 1],
                        ['width' => '14%', 'targets' => 2],
                        ['width' => '2%', 'targets' => 3],
                        ['width' => '7%', 'targets' => 5],
                        ['width' => '5%', 'targets' => 6],
                        ['width' => '10%', 'targets' => 7],
                        ['width' => '1%', 'targets' => 8],
        ]]);
    }

    /**
     * Creates select for types
     * 
     * @return array
     */
    protected function types(): array
    {
        return array_merge(['' => 'All'], NotificationTypes::all(['name', 'title'])->pluck('title', 'name')->toArray());
    }

    /**
     * Get actions column for table builder.
     * 
     * @return callable
     */
    protected function getActionsColumn()
    {
        return function ($row) {
            $html    = app('html');
            $btns    = [
                $html->create('li', $html->link(handles("antares::notifications/logs/preview/" . $row->id), trans('antares/notifications::logs.actions.preview'), ['data-notification' => !in_array($row->type, ['Email', 'Sms']), 'data-icon' => 'desktop-windows', 'class' => "triggerable preview-notification-log"])),
                $html->create('li', $html->link(handles("antares::notifications/logs/" . $row->id . "/delete"), trans('antares/notifications::logs.actions.delete'), ['class' => "triggerable confirm", 'data-icon' => 'delete', 'data-title' => trans("antares/notifications::logs.are_you_sure"), 'data-description' => trans('antares/notifications::logs.delete_notification_log_desc', ['id' => $row->id])]))
            ];
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu', 'data-id' => $row->id])->get();

            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}
