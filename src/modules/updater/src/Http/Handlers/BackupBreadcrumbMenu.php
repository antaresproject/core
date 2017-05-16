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

use Antares\Contracts\Authorization\Authorization;
use Antares\Foundation\Support\MenuHandler;

class BackupBreadcrumbMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'   => 'logger-backups-breadcrumb',
        'link' => 'antares::updater/backups',
        'icon' => null,
        'boot' => [
            'group' => 'menu.top.updater',
            'on'    => 'antares/updater::admin.backup.index'
        ]
    ];

    /**
     * Get the title.
     * @param  string  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        return trans('antares/updater::messages.breadcrumb.backups');
    }

    /**
     * Checks authorization to display the menu.
     * 
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        $acl = app('antares.acl')->make('antares/updater');
        return $acl->can('backups-list') && $acl->can('restore-backup');
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
        $additionalClass = 'confirm';
        $url             = 'antares::updater/backups/create';
        if (env('APP_DEMO')) {
            $additionalClass = 'backup-disabled';
            $url             = '#';
        }
        $this->handler
                ->add('create-backup-breadcrumb', '^:logger-backups-breadcrumb')
                ->title(trans('antares/updater::messages.breadcrumb.create_backup'))
                ->icon('zmdi-plus-circle-o')
                ->link(handles($url))
                ->attributes([
                    'class'            => 'triggerable ' . $additionalClass,
                    'data-title'       => trans('antares/updater::messages.ask_for_create_backup'),
                    'data-description' => trans('antares/updater::messages.create_backup_description'),
                    'data-disabled'    => trans('antares/updater::messages.backup_disabled_for_demo'),
        ]);
    }

}
