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

class UserEditBreadcrumbMenu extends MenuHandler
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
        'id'   => 'user-edit',
        'link' => '#',
        'icon' => 'fa-users',
        'boot' => [
            'group' => 'menu.top.user',
            'on'    => 'antares/foundation::users.edit'
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
        return is_null($user) ? '' : $user->fullname;
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
     * Gets entity attribute
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getEntityAttribute()
    {
        return $this->user;
    }

    /**
     * User getter
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getUser()
    {
        if (is_null($this->user)) {
            $uid        = from_route('users');
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
        $user = $this->getUser();
        if (!$this->passesAuthorization() or is_null($user)) {
            return;
        }
        $this->createMenu();
        if (!app('antares.acl')->make('antares')->can('client-create')) {
            return;
        }
        $acl            = app('antares.acl')->make('antares/control');
        $canDeleteUser  = $acl->can('user-delete');
        $canLoginAsUser = $acl->can('login-as-user');
        //$canViewUser    = $acl->can('login-as-user');


        $uid = from_route('users');
        if ($canDeleteUser) {
            $this->handler
                    ->add('user-delete', '^:user-edit')
                    ->title(trans('antares/foundation::label.delete'))
                    ->link(handles("antares::users/{$uid}/delete", ['csrf' => true]))
                    ->icon('zmdi-delete')
                    ->attributes([
                        'class'            => "triggerable confirm",
                        'data-title'       => trans("Are you sure?"),
                        'data-description' => trans('Deleteing user') . ' ' . $user->fullname]);
        }

        if (auth()->user()->id !== $uid && $canLoginAsUser) {
            $this->handler
                    ->add('user-login-as', '^:user-edit')
                    ->title(trans('antares/control::label.login_as', ['fullname' => $user->fullname]))
                    ->link(handles("login/with/{$uid}"))
                    ->icon('zmdi-odnoklassniki')
                    ->attributes([
                        'class'            => 'triggerable confirm',
                        'data-title'       => trans("Are you sure?"),
                        'data-description' => trans('antares/control::label.login_as', ['fullname' => $user->fullname])]
            );
        }
        $actions = $this->user->dependableActions();

        foreach ($actions as $action) {
            $item = $this->handler
                    ->add(snake_case($action['title']), '^:user-edit')
                    ->title($action['title'])
                    ->link($action['url']);
            if (isset($action['icon'])) {
                $item->icon($action['icon']);
            }
            if (isset($action['attributes'])) {
                $item->attributes($action['attributes']);
            }
        }
    }

}
