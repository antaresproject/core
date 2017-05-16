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

namespace Antares\Notifications\Event;

use Antares\Foundation\Template\SendableNotification;
use Antares\Foundation\Template\CustomNotification;
use Antares\Notifications\Model\NotificationTypes;
use Antares\Foundation\Template\EmailNotification;
use Antares\View\Contracts\NotificationContract;
use Antares\Foundation\Template\SmsNotification;
use Illuminate\Foundation\Bus\DispatchesJobs;

class EventDispatcher
{

    use DispatchesJobs;

    /**
     * sends notification
     * 
     * @param array $notification
     * @return boolean
     */
    public function run($notification, $variables = null, $recipients = null)
    {
        $name = NotificationTypes::whereId($notification['type_id'])->first()->name;
        switch ($name) {
            case 'email':
                $instance = app(EmailNotification::class);
                break;
            case 'sms':
                $instance = app(SmsNotification::class);
                break;
            default:
                $instance = app(CustomNotification::class);
                break;
        }
        $instance->setPredefinedVariables($variables);
        $instance->setModel($notification);

        if ($instance instanceof SendableNotification) {
            $instance->setRecipients($recipients);

            if (!$this->validate($instance)) {
                return false;
            }
            $type = $instance->getType();
            $job  = $instance->onConnection('database')->onQueue($type);
            $this->dispatch($job);
        } else {
            return $instance->handle();
        }
    }

    /**
     * validates notification
     * 
     * @param NotificationContract $instance
     * @return boolean
     */
    protected function validate($instance)
    {
        return $instance instanceof NotificationContract;
    }

}
