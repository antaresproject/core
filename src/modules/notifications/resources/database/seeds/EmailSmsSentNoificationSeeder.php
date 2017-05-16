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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
use Antares\Notifier\Seeder\NotificationSeeder;
use Illuminate\Support\Facades\DB;

class EmailSmsSentNoificationSeeder extends NotificationSeeder
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
                'type'     => 'admin',
                'severity' => 'high',
                'event'    => 'sms.notification_not_sent',
                'contents' => [
                    'en' => [
                        'title'   => 'Sms notification send failed',
                        'content' => file_get_contents(__DIR__ . '/../../views/notification/sms_notification_not_sent.twig')
                    ],
                ]
            ]);

            $this->addNotification([
                'category' => 'default',
                'type'     => 'admin',
                'severity' => 'medium',
                'event'    => 'sms.notification_sent',
                'contents' => [
                    'en' => [
                        'title'   => 'Sms notification send success',
                        'content' => file_get_contents(__DIR__ . '/../../views/notification/sms_notification_sent.twig')
                    ],
                ]
            ]);

            $this->addNotification([
                'category' => 'default',
                'type'     => 'admin',
                'severity' => 'high',
                'event'    => 'email.notification_not_sent',
                'contents' => [
                    'en' => [
                        'title'   => 'Email notification send failed',
                        'content' => file_get_contents(__DIR__ . '/../../views/notification/email_notification_not_sent.twig')
                    ],
                ]
            ]);

            $this->addNotification([
                'category' => 'default',
                'type'     => 'admin',
                'severity' => 'medium',
                'event'    => 'email.notification_sent',
                'contents' => [
                    'en' => [
                        'title'   => 'Email notification send success',
                        'content' => file_get_contents(__DIR__ . '/../../views/notification/email_notification_sent.twig')
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
                    'email.notification_not_sent',
                    'email.notification_sent',
                    'sms.notification_not_sent',
                    'sms.notification_sent'
        ]);
    }

}
