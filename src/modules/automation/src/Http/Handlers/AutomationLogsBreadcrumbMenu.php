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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation\Http\Handlers;

use Antares\Contracts\Authorization\Authorization;
use Antares\Foundation\Support\MenuHandler;
use Antares\Automation\Model\JobResults;

class AutomationLogsBreadcrumbMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'   => 'automation-log-breadcrumb',
        'link' => 'antares::automations/logs/index',
        'icon' => null,
        'boot' => [
            'group' => 'menu.top.automation-logs',
            'on'    => 'antares/automation::admin.index.logs'
        ]
    ];

    /**
     * Get the title.
     * @param  string  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        return trans('antares/automation::messages.breadcrumb.automation_log');
    }

    /**
     * Check authorization to display the menu.
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return true;
        return app('antares.acl')->make('antares/automation')->can('download-logs');
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
        $count = app(JobResults::class)->count();
        if ($count) {
            $this->handler
                    ->add('automation-log-download', '^:automation-log-breadcrumb')
                    ->title(trans('antares/automation::messages.breadcrumb.automation_logs_download'))
                    ->icon('zmdi-download')
                    ->link(handles('antares::automation/logs/download'))
                    ->attributes(['class' => 'triggerable download-automation-log']);

            $this->handler
                    ->add('automation-log-delete', '^:automation-log-breadcrumb')
                    ->title(trans('antares/automation::messages.breadcrumb.automation_logs_delete'))
                    ->icon('zmdi-delete')
                    ->link(handles('antares::automation/logs/delete'))
                    ->attributes(['class' => 'delete-logs-menu-breadcrumb']);
        }
    }

}
