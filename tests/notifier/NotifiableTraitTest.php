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

namespace Antares\Notifier\TestCase;

use Mockery as m;
use Antares\Notifier\Message;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Antares\Support\Facades\Notifier;

class NotifiableTraitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $app = new Container();

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
    }

    /**
     * Test \Antares\Notifier\NotifiableTrait::sendNotification()
     * method.
     *
     * @test
     */
    public function testSendNotificationTraitMethod()
    {
        $user     = m::mock('\Antares\Contracts\Notification\Recipient', '\Illuminate\Contracts\Support\Arrayable');
        $notifier = m::mock('\Antares\Contracts\Notification\Notification');
        $stub     = new Notifiable();

        $user->shouldReceive('toArray')->twice()->andReturn([
            'id'       => 2,
            'fullname' => 'Administrator',
        ]);

        $notifier->shouldReceive('send')->twice()
                ->with($user, m::type('\Antares\Contracts\Notification\Message'))->andReturn(true);

        Notifier::swap($notifier);

        $this->assertTrue($stub->notify($user));
        $this->assertTrue($stub->notifyFluent($user));
    }

}

class Notifiable
{

    use \Antares\Notifier\NotifiableTrait;

    public function notify($user)
    {
        return $this->sendNotification($user, 'foo', 'email.foo', []);
    }

    public function notifyFluent($user)
    {
        return $this->sendNotification($user, new Message(['view' => 'email.foo', 'data' => [], 'subject' => 'foo']));
    }

}
