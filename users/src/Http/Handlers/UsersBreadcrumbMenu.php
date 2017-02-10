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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Users\Http\Handlers;

use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Authorization\Authorization;

class UsersBreadcrumbMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'clients',
        'title' => 'Users',
        'link'  => '#',
        'icon'  => 'fa-users',
        'boot'  => [
            'group' => 'menu.top.clients',
            'on'    => 'antares/foundation::users.index'
        ]
    ];

    /**
     * Get the title.
     * @param  string  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        return trans('antares/users::global.' . $value);
    }

    /**
     * Check authorization to display the menu.
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return $acl->can('clients-list');
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
        if (!app('antares.acl')->make('antares')->can('client-create')) {
            return;
        }
        $this->handler
                ->add('client-add', '^:clients')
                ->title('Add User')
                ->link(handles('antares::users/create'))
                ->icon('zmdi-account-add');
    }

}
