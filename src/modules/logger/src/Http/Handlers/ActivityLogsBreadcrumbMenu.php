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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Logger\Http\Handlers;

use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Authorization\Authorization;

class ActivityLogsBreadcrumbMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'   => 'logger-breadcrumb',
        'link' => 'antares::logger/activity/index',
        'icon' => null,
        'boot' => [
            'group' => 'menu.top.logger',
            'on'    => 'antares/logger::admin.activity.index'
        ]
    ];

    /**
     * Get the title.
     * @param  string  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        return trans('antares/logger::messages.breadcrumb.activity_log');
    }

    /**
     * Check authorization to display the menu.
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return true;
        return app('antares.acl')->make('antares/logger')->can('download-logs');
    }

    /**
     * Create a handler.
     * @return void
     */
    public function handle()
    {
        if (!$this->passesAuthorization()) {
            return;
        }
        $this->createMenu();
        $this->handler
                ->add('activyt-log-download', '^:logger-breadcrumb')
                ->title(trans('antares/logger::messages.activity_logs_download'))
                ->icon('zmdi-download')
                ->link(handles('antares::logger/activity/download'))
                ->attributes(['class' => 'triggerable download-activity-log']);

        $this->handler
                ->add('activyt-log-delete', '^:logger-breadcrumb')
                ->title(trans('antares/logger::messages.activity_logs_delete'))
                ->icon('zmdi-delete')
                ->link(handles('antares::logger/activity/delete'))
                ->attributes(['class' => 'delete-logs-menu-breadcrumb']);
    }

}
