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

namespace Antares\Notifier\Handlers\TestCase;

use Antares\Testing\ApplicationTestCase;
use Antares\Notifier\Handlers\Antares;
use Antares\Notifier\Message;
use Mockery as m;

class AntaresTest extends ApplicationTestCase
{

    /**
     * Test Antares\Notifier\AntaresNotifier::send() method without
     * queue.
     *
     * @test
     */
    public function testSendMethodWithoutQueue()
    {
        $notifier = m::mock('\Antares\Notifier\Mailer');

        $user = m::mock('\Antares\Contracts\Notification\Recipient');

        $subject = 'foobar';
        $view    = 'foo.bar';
        $data    = [];



        $notifier->shouldReceive('send')->andReturn($receipt = m::mock(\Antares\Notifier\Receipt::class));
        $receipt->shouldReceive('usingQueue')->once()->andReturnSelf()
                ->shouldReceive('sent')->andReturn(true);
        $stub    = new Antares($notifier);
        $receipt = $stub->send($user, new Message(compact('subject', 'view', 'data')));
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $receipt);
        $this->assertTrue($receipt->sent());
    }

    /**
     * Test Antares\Notifier\AntaresNotifier::send() method with callback.
     *
     * @test
     */
    public function testSendMethodWithCallback()
    {

        $notifier = m::mock('\Antares\Notifier\Mailer')->makePartial();
        $user     = m::mock('\Antares\Contracts\Notification\Recipient');

        $view = 'foo.bar';
        $data = [];

        $callback = function ($mail) {
            $mail->subject('foobar!!');
        };





        $notifier->shouldReceive('send')->andReturn($receipt = m::mock(\Antares\Notifier\Receipt::class));
        $receipt->shouldReceive('usingQueue')->once()->andReturnSelf()
                ->shouldReceive('sent')->andReturn(true);


        $stub    = new Antares($notifier);
        $receipt = $stub->send($user, new Message(compact('view', 'data')), $callback);

        $this->assertInstanceOf('\Antares\Notifier\Receipt', $receipt);
        $this->assertTrue($receipt->sent());
    }

    /**
     * Test Antares\Notifier\AntaresNotifier::send() method using
     * queue.
     *
     * @test
     */
    public function testSendMethodUsingQueue()
    {
        $mailer = m::mock('\Illuminate\Contracts\Mail\Mailer');

        $notifier = m::mock('\Antares\Notifier\Mailer')->makePartial();
        $memory   = m::mock('\Antares\Contracts\Memory\Provider');
        $user     = m::mock('\Antares\Contracts\Notification\Recipient');

        $subject = 'foobar';
        $view    = 'foo.bar';
        $data    = [];


        $memory->shouldReceive('get')->once()->with('email.queue', false)->andReturn(true)
                ->shouldReceive('get')->once()->with('email.driver')->andReturn('mail');

        $mailer->shouldReceive('failures')->never();


        $notifier->shouldReceive('send')->andReturn($receipt = m::mock(\Antares\Notifier\Receipt::class));
        $receipt->shouldReceive('usingQueue')->once()->andReturnSelf()
                ->shouldReceive('sent')->andReturn(true);

        $stub    = new Antares($notifier);
        $stub->attach($memory);
        $receipt = $stub->send($user, new Message(compact('subject', 'view', 'data')));

        $this->assertInstanceOf('\Antares\Notifier\Receipt', $receipt);
        $this->assertTrue($receipt->sent());
    }

    /**
     * Test Antares\Notifier\AntaresNotifier::send() method failed.
     *
     * @test
     */
    public function testSendMethodFailed()
    {


        $notifier = m::mock('\Antares\Notifier\Mailer')->makePartial();
        $user     = m::mock('\Antares\Contracts\Notification\Recipient');

        $subject = 'foobar';
        $view    = 'foo.bar';
        $data    = [];

        $notifier->shouldReceive('send')->andReturn($receipt = m::mock(\Antares\Notifier\Receipt::class));
        $receipt->shouldReceive('usingQueue')->once()->andReturnSelf()
                ->shouldReceive('sent')->andReturn(true);


        $stub    = new Antares($notifier);
        $receipt = $stub->send($user, new Message(compact('subject', 'view', 'data')));

        $this->assertInstanceOf('\Antares\Notifier\Receipt', $receipt);
        $this->assertTrue($receipt->sent());
    }

}
