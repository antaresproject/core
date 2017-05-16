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

namespace Antares\Notifications\Listener;

use Antares\Notifications\Repository\Repository;
use Antares\Notifications\Event\EventDispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;

class NotificationsListener
{

    use DispatchesJobs;

    /**
     * Repository instance
     *
     * @var Repository 
     */
    protected $repository;

    /**
     * Construct
     * 
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Listening for notifications
     */
    public function listen()
    {
        $notifications = $this->repository->findSendable()->toArray();
        foreach ($notifications as $notification) {
            $this->listenNotificationsEvents($notification);
        }
    }

    /**
     * runs notification events
     * 
     * @param array $notification
     * @return boolean
     */
    protected function listenNotificationsEvents(array $notification)
    {
        is_null($events = array_get($notification, 'event')) ? $events = app($notification['classname'])->getEvents() : null;
        foreach ((array) $events as $event) {
            $this->runNotificationListener($event, $notification);
        }
    }

    /**
     * Runs notification listener
     * 
     * @param String $event
     * @param Model\Notifications $notification
     */
    protected function runNotificationListener($event, $notification)
    {
        app('events')->listen($event, function($variables = null, $recipients = null) use($notification) {
            app(EventDispatcher::class)->run($notification, $variables, $recipients);
        });
    }

}
