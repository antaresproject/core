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

namespace Antares\Notifications\Http\Handlers;

use Antares\Contracts\Authorization\Authorization;
use Antares\Foundation\Support\MenuHandler;

class NotificationsBreadcrumbMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'notifications',
        'title' => 'Notifications',
        'link'  => '#',
        'icon'  => '',
        'boot'  => [
            'on'    => 'antares/notifications::admin.index.index',
            'group' => 'menu.top.notifications'
        ]
    ];

    /**
     * Get the title.
     * 
     * @return string
     */
    public function getTitleAttribute()
    {
        return trans('antares/notifications::messages.notification_templates');
    }

    /**
     * Check authorization to display the menu.
     * @param  Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return app('antares.acl')->make('antares/notifications')->can('notifications-create');
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

        $acl                   = app('antares.acl')->make('antares/notifications');
        $canCreateNotification = $acl->can('notifications-create');
        if (!$canCreateNotification) {
            return;
        }
        if ($canCreateNotification) {
            $this->handler
                    ->add('notifications-create', '^:notifications')
                    ->title(trans('antares/notifications::messages.notification_templates_create'))
                    ->link(handles('antares::notifications/create/' . area()))
                    ->icon('zmdi-notifications-add');
        }
    }

}
