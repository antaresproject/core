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


namespace Antares\Contracts\Auth\Listener;

use Illuminate\Contracts\Auth\Authenticatable;

interface AuthenticateUser
{

    /**
     * Response to user log-in trigger failed validation .
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function userLoginHasFailedValidation($errors);

    /**
     * Response to user has logged in successfully.
     *
     * @param String $redirect
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     *
     * @return mixed
     */
    public function userHasLoggedIn(Authenticatable $user, $redirect);
}
