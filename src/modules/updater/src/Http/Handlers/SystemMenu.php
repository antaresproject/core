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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Logger\Http\Handlers;

use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Authorization\Authorization;

class SystemMenu extends MenuHandler
{

    /**
     * Check authorization to display the menu.
     *
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     *
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return $acl->can('manage-antares');
    }

    public function handle()
    {
        $updaterAcl = app('antares.acl')->make('antares/updater');
        if ($updaterAcl->can('update-system') && $updaterAcl->can('update-module')) {
            $this->handler->add('updates', '>:system.system_informations')
                    ->link(handles('antares::updater/update'))
                    ->title(trans('antares/updater::global.updates'))
                    ->icon('zmdi-arrow-merge');
        }

        if (!app('request')->get('sandbox')) {
            if ($updaterAcl->can('backups-list') && $updaterAcl->can('restore-backup')) {
                $this->handler->add('backups', '>:system.updates')
                        ->link(handles('antares::updater/backups'))
                        ->title(trans('antares/updater::global.backups'))
                        ->icon('zmdi-refresh-sync');
            }

            if ($updaterAcl->can('sandbox-dashboard')) {

                $this->handler->add('sandboxes', '>:system.backups')
                        ->link(handles('antares::updater/sandboxes'))
                        ->title(trans('antares/updater::global.sandboxes'))
                        ->icon('zmdi-cloud-box');
            }
        }
    }

}
