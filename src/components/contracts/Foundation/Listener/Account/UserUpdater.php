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

interface UserUpdater extends User
{
    /**
     * Response when update user page succeed.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showUserChanger(array $data);

    /**
     * Response when update user failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     * @param  string|int  $id
     *
     * @return mixed
     */
    public function updateUserFailedValidation($errors, $id);

    /**
     * Response when updating user failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function updateUserFailed(array $errors);

    /**
     * Response when updating user succeed.
     *
     * @return mixed
     */
    public function userUpdated();
}
