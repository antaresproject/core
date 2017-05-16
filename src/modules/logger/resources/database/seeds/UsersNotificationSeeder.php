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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
use Antares\Notifier\Seeder\NotificationSeeder;
use Illuminate\Support\Facades\DB;

class UsersNotificationSeeder extends NotificationSeeder
{

    /**
     * Dodaje dane do tabel
     *
     * @return void
     */
    public function run()
    {

        DB::beginTransaction();
        try {
            $this->down();
            $this->addNotification([
                'category' => 'default',
                'severity' => 'medium',
                'event'    => 'notification.user_has_been_created',
                'contents' => [
                    'en' => [
                        'title'   => 'User has been created',
                        'content' => 'User [[ user.firstname ]] [[ user.lastname ]] has been created.'
                    ],
                ]
            ]);
            $this->addNotification([
                'category' => 'default',
                'severity' => 'high',
                'event'    => 'notification.user_has_not_been_created',
                'contents' => [
                    'en' => [
                        'title'   => 'User has not been created',
                        'content' => 'User [[ user.firstname ]] [[ user.lastname ]] has not been created.'
                    ],
                ]
            ]);
            $this->addNotification([
                'category' => 'default',
                'severity' => 'medium',
                'event'    => 'notification.user_has_been_deleted',
                'contents' => [
                    'en' => [
                        'title'   => 'User has been deleted',
                        'content' => 'User [[ user.firstname ]] [[ user.lastname ]] has been deleted.'
                    ],
                ]
            ]);
            $this->addNotification([
                'category' => 'default',
                'severity' => 'high',
                'event'    => 'notification.user_has_not_been_deleted',
                'contents' => [
                    'en' => [
                        'title'   => 'User has not been deleted',
                        'content' => 'User [[ user.firstname ]] [[ user.lastname ]] has not been deleted.'
                    ],
                ]
            ]);
            $this->addNotification([
                'category' => 'default',
                'severity' => 'medium',
                'event'    => 'notification.user_has_been_updated',
                'contents' => [
                    'en' => [
                        'title'   => 'User has been updated',
                        'content' => 'User [[ user.firstname ]] [[ user.lastname ]] has been updated.'
                    ],
                ]
            ]);
            $this->addNotification([
                'category' => 'default',
                'severity' => 'high',
                'event'    => 'notification.user_has_not_been_updated',
                'contents' => [
                    'en' => [
                        'title'   => 'User has not been updated',
                        'content' => 'User [[ user.firstname ]] [[ user.lastname ]] has not been updated.'
                    ],
                ]
            ]);
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }

        DB::commit();
    }

    /**
     * Usuwa dane do tabel
     *
     * @return void
     */
    public function down()
    {
        return $this->deleteNotificationByEventName([
                    'notification.user_has_been_created',
                    'notification.user_has_not_been_created',
                    'notification.user_has_been_updated',
                    'notification.user_has_not_been_updated',
                    'notification.user_has_been_deleted',
                    'notification.user_has_not_been_deleted',
        ]);
    }

}
