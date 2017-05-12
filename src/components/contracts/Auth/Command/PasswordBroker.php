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
 namespace Antares\Contracts\Auth\Command;

use Antares\Contracts\Auth\Listener\PasswordReset;
use Antares\Contracts\Auth\Listener\PasswordResetLink;

interface PasswordBroker
{
    /**
     * Request to reset password.
     *
     * @param  \Antares\Contracts\Auth\Listener\PasswordResetLink  $listener
     * @param  array  $input
     *
     * @return mixed
     */
    public function store(PasswordResetLink $listener, array $input);

    /**
     * Reset the password.
     *
     * @param  \Antares\Contracts\Auth\Listener\PasswordReset  $listener
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(PasswordReset $listener, array $input);
}
