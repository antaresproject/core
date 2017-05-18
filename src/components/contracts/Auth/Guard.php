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
 namespace Antares\Contracts\Auth;

use Illuminate\Contracts\Auth\Guard as GuardContract;

interface Guard extends GuardContract
{
    /**
     * Get the current user's roles of the application.
     *
     * If the user is a guest, empty array should be returned.
     *
     * @return array
     */
    public function roles();

    /**
     * Determine if current user has the given role.
     *
     * @param  string|array  $roles
     *
     * @return bool
     */
    public function is($roles);
}
