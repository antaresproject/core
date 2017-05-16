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



namespace Antares\Logger\Listeners;

use Antares\Logger\Factory;
use Illuminate\Events\Dispatcher;
use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Auth\Events\Logout as LogoutEvent;

class UserAuthListener
{

    /**
     * Logger factory instance.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * KeepListener constructor.
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Handle user login events.
     *
     * @param LoginEvent $event
     */
    public function onUserLogin(LoginEvent $event)
    {
        $this->factory->keep('low');
    }

    /**
     * Handle user logout events.
     *
     * @param LogoutEvent $event
     */
    public function onUserLogout(LogoutEvent $event)
    {
        $this->factory->keep('low');
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(LoginEvent::class, UserAuthListener::class . '@onUserLogin');
        $events->listen(LogoutEvent::class, UserAuthListener::class . '@onUserLogout');
    }

}
