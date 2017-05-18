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

interface ProfileUpdater extends User
{
    /**
     * Response to show user profile changer.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showProfileChanger(array $data);

    /**
     * Response when validation on update profile failed.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function updateProfileFailedValidation($errors);

    /**
     * Response when update profile failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function updateProfileFailed(array $errors);

    /**
     * Response when update profile succeed.
     *
     * @return mixed
     */
    public function profileUpdated();
}
