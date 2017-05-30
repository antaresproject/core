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

interface PasswordUpdater extends User
{
    /**
     * Response to show user password.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showPasswordChanger(array $data);

    /**
     * Response when validation on change password failed.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function updatePasswordFailedValidation($errors);

    /**
     * Response when verify current password failed.
     *
     * @return mixed
     */
    public function verifyCurrentPasswordFailed();

    /**
     * Response when update password failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function updatePasswordFailed(array $errors);

    /**
     * Response when update password succeed.
     *
     * @return mixed
     */
    public function passwordUpdated();
}
