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

use Antares\Contracts\Authorization\Authorization;
use Antares\Foundation\Support\MenuHandler;
use Antares\Model\User;

class UserViewBreadcrumbMenu extends MenuHandler
{

    /**
     * User instance
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $user;

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'   => 'user-view',
        'link' => '#',
        'icon' => 'fa-users',
        'boot' => [
            'group' => 'menu.top.user',
            'on'    => 'antares/foundation::users.show'
        ]
    ];

    /**
     * Get the title.
     * @param  string  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        $user = $this->getUser();
        return '#' . $user->id . ' ' . $user->fullname;
    }

    /**
     * Gets entity attribute
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getEntityAttribute()
    {
        return $this->user;
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
     * User getter
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getUser()
    {
        if (is_null($this->user)) {
            $uid        = from_route('users', from_route('user'));
            $this->user = User::select(['id', 'firstname', 'lastname', 'status'])->whereId($uid)->first();
        }
        return $this->user;
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

        $acl           = app('antares.acl')->make('antares/control');
        $canUpdateUser = $acl->can('user-update');
        $canDeleteUser = $acl->can('user-delete');
        $user          = $this->getUser();



        $uid = from_route('user');
        if ($canUpdateUser) {
            $this->handler
                    ->add('user-edit', '^:user-view')
                    ->title(trans('antares/foundation::label.edit'))
                    ->link(handles("antares::users/{$uid}/edit"))
                    ->icon('zmdi-edit');
        }
        if ($canDeleteUser) {
            $this->handler
                    ->add('user-delete', '^:user-view')
                    ->title(trans('antares/foundation::label.delete'))
                    ->link(handles("antares::users/{$uid}/delete", ['csrf' => true]))
                    ->icon('zmdi-delete')
                    ->attributes([
                        'class'            => "triggerable confirm",
                        'data-title'       => trans("Are you sure?"),
                        'data-description' => trans('Deleteing user') . ' ' . $user->fullname]);
        }
    }

}
