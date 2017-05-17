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
 namespace Antares\Contracts\Foundation\Listener\Account;

interface UserCreator
{
    /**
     * Response when create user page succeed.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showUserCreator(array $data);

    /**
     * Response when storing user failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function createUserFailedValidation($errors);

    /**
     * Response when storing user failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function createUserFailed(array $errors);

    /**
     * Response when storing user succeed.
     *
     * @return mixed
     */
    public function userCreated();
}
