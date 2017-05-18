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

interface ProfileCreator
{

    /**
     * Response when create a user failed validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function createProfileFailedValidation($errors);

    /**
     * Response when create a user failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function createProfileFailed(array $errors);

    /**
     * Response when create a user succeed but unable to notify the user.
     *
     * @return mixed
     */
    public function profileCreatedWithoutNotification();

    /**
     * Response when create a user succeed with notification.
     *
     * @return mixed
     */
    public function profileCreated();
}
