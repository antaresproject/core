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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
 namespace Antares\Contracts\Authorization;

interface Authorization
{
    /**
     * Assign single or multiple $roles + $actions to have access.
     *
     * @param  string|array  $roles
     * @param  string|array  $actions
     * @param  bool  $allow
     *
     * @return $this
     */
    public function allow($roles, $actions, $allow = true);

    /**
     * Verify whether current user has sufficient roles to access the
     * actions based on available type of access.
     *
     * @param  string  $action
     *
     * @return bool
     */
    public function can($action);

    /**
     * Verify whether given roles has sufficient roles to access the
     * actions based on available type of access.
     *
     * @param  string|array  $roles
     * @param  string  $action
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function check($roles, $action);

    /**
     * Shorthand function to deny access for single or multiple
     * $roles and $actions.
     *
     * @param  string|array  $roles
     * @param  string|array  $actions
     *
     * @return $this
     */
    public function deny($roles, $actions);
}
