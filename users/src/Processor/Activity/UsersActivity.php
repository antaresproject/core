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


namespace Antares\Users\Processor\Activity;

use Antares\Model\User;
use Antares\Model\UserActivity;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;

class UsersActivity
{

    /**
     * Fired when user is just logged in
     *
     * @param Login $login
     */
    public function onLoginHandler(Login $login)
    {
        /** @var User $user */
        $user = $login->user;
        $this->updateActivity($user);
    }

    /**
     * Registering activity for passed user
     *
     * @param User $user
     */
    public function updateActivity(User $user)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $activity = $user->activity;
        if(!$activity instanceof UserActivity) {
            $activity = new UserActivity();
        }
        $activity->last_activity = $now;
        $activity->user_id = $user->id;
        $activity->save();
    }

}