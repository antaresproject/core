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


namespace Antares\Users\Auth;

use Illuminate\Support\Arr;
use Antares\Contracts\Auth\Command\ThrottlesLogins as Command;

class WithoutThrottle extends ThrottlesLogins implements Command
{

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @return bool
     */
    public function hasTooManyLoginAttempts()
    {
        return false;
    }

    /**
     * Get total seconds before doing another login attempts for the user.
     *
     * @return int
     */
    public function getSecondsBeforeNextAttempts()
    {
        return Arr::get(static::$config, 'locked_for', 60);
    }

    /**
     * Increment the login attempts for the user.
     *
     * @return void
     */
    public function incrementLoginAttempts()
    {
        //
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @return void
     */
    public function clearLoginAttempts()
    {
        //
    }

}
