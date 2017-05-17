<?php

/**
 * Part of the Antares package.
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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Auth;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Auth\SessionGuard as BaseGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Antares\Contracts\Auth\Guard as GuardContract;

class SessionGuard extends BaseGuard implements StatefulGuard, GuardContract
{

    /**
     * Cached user to roles relationship.
     *
     * @var array
     */
    protected $userRoles = null;

    /**
     * Setup roles event listener.
     *
     * @param  \Closure|string  $event
     *
     * @return void
     */
    public function setup($event)
    {
        $this->userRoles = null;
        $this->events->forget('antares.auth: roles');
        $this->events->listen('antares.auth: roles', $event);
    }

    /**
     * Get the current user's roles of the application.
     *
     * If the user is a guest, empty array should be returned.
     *
     * @return array
     */
    public function roles()
    {
        $user   = $this->user();
        $userId = 0;
        is_null($user) || $userId = $user->getAuthIdentifier();
        $roles  = Arr::get($this->userRoles, "{$userId}", []);
        if (empty($roles)) {
            $roles = $this->getUserRolesFromEventDispatcher($user, $roles);
        }

        Arr::set($this->userRoles, "{$userId}", $roles);

        return $roles;
    }

    /**
     * Determine if current user has the given role.
     *
     * @param  string|array  $roles
     *
     * @return bool
     */
    public function is($roles)
    {
        $userRoles = $this->roles();

        // For a pre-caution, we should return false in events where user
        // roles not an array.
        if (!is_array($userRoles)) {
            return false;
        }

        // We should ensure that all given roles match the current user,
        // consider it as a AND condition instead of OR.
        foreach ((array) $roles as $role) {
            if (!in_array($role, $userRoles)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if current user has any of the given role.
     *
     * @param  array   $roles
     *
     * @return bool
     */
    public function isAny(array $roles)
    {
        $userRoles = $this->roles();

        // For a pre-caution, we should return false in events where user
        // roles not an array.
        if (!is_array($userRoles)) {
            return false;
        }

        // We should ensure that any given roles match the current user,
        // consider it as OR condition.
        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if current user does not has any of the given role.
     *
     * @param  string   $roles
     *
     * @return bool
     */
    public function isNot($roles)
    {
        return !$this->is($roles);
    }

    /**
     * Determine if current user does not has any of the given role.
     *
     * @param  array   $roles
     *
     * @return bool
     */
    public function isNotAny(array $roles)
    {
        return !$this->isAny($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        parent::logout();

        // We should flush the cached user roles relationship so any
        // subsequent request would re-validate all information,
        // instead of referring to the cached value.
        $this->userRoles = null;
    }

    /**
     * Ger user roles from event dispatcher.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $roles
     *
     * @return array
     */
    protected function getUserRolesFromEventDispatcher(Authenticatable $user = null, $roles = [])
    {

        $roles = $this->events->until('antares.auth: roles', [$user, (array) $roles]);
        if (!is_null($user)) {
            $roles = $user->roles->pluck('name', 'id')->toArray();
        }

        // It possible that after event are all propagated we don't have a
        // roles for the user, in this case we should properly append "Guest"
        // user role to the current user.
        if (is_null($roles)) {
            return ['Guest'];
        }

        return ($roles instanceof Arrayable ? $roles->toArray() : $roles);
    }

}
