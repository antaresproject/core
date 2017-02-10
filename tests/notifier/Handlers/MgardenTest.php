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
 namespace Antares\Notifier\Handlers\TestCase;

use Mockery as m;
use Antares\Notifier\Message;
use Antares\Notifier\Receipt;
use Antares\Notifier\Handlers\Antares;

class AntaresTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Notifier\AntaresNotifier::send() method without
     * queue.
     *
     * @test
     */
    public function testSendMethodWithoutQueue()
    {
        $mailer   = m::mock('\Illuminate\Contracts\Mail\Mailer');
        $notifier = m::mock('\Antares\Notifier\Mailer')->makePartial();
        $message  = m::mock('\Illuminate\Mail\Message');
        $user     = m::mock('\Antares\Contracts\Notification\Recipient');

        $subject = 'foobar';
        $view    = 'foo.bar';
        $data    = [];

        $user->shouldReceive('getRecipientEmail')->once()->andReturn('hello@antaresplatform.com')
            ->shouldReceive('getRecipientName')->once()->andReturn('Administrator');
        $message->shouldReceive('to')->once()->with('hello@antaresplatform.com', 'Administrator')->andReturnNull()
            ->shouldReceive('subject')->once()->with($subject)->andReturnNull();
        $mailer->shouldReceive('failures')->once()->andReturn([]);
        $notifier->shouldReceive('push')->once()->with($view, $data, m::type('Closure'))
                ->andReturnUsing(function ($v, $d, $c) use ($mailer, $message) {
                    $c($message);

                    return new Receipt($mailer, false);
                });

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
        $mailer   = m::mock('\Illuminate\Contracts\Mail\Mailer');
        $message  = m::mock('\Illuminate\Mail\Message');
        $notifier = m::mock('\Antares\Notifier\Mailer')->makePartial();
        $user     = m::mock('\Antares\Contracts\Notification\Recipient');

        $view = 'foo.bar';
        $data = [];

        $callback = function ($mail) {
            $mail->subject('foobar!!');
        };

        $user->shouldReceive('getRecipientEmail')->once()->andReturn('hello@antaresplatform.com')
            ->shouldReceive('getRecipientName')->once()->andReturn('Administrator');
        $message->shouldReceive('to')->once()->with('hello@antaresplatform.com', 'Administrator')->andReturnNull()
            ->shouldReceive('subject')->once()->with('foobar!!')->andReturnNull();
        $mailer->shouldReceive('failures')->once()->andReturn([]);
        $notifier->shouldReceive('push')->once()->with($view, $data, m::type('Closure'))
                ->andReturnUsing(function ($v, $d, $c) use ($mailer, $message) {
                    $c($message);

                    return new Receipt($mailer, false);
                });

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
        $mailer   = m::mock('\Illuminate\Contracts\Mail\Mailer');
        $message  = m::mock('\Illuminate\Mail\Message');
        $notifier = m::mock('\Antares\Notifier\Mailer')->makePartial();
        $memory   = m::mock('\Antares\Contracts\Memory\Provider');
        $user     = m::mock('\Antares\Contracts\Notification\Recipient');

        $subject = 'foobar';
        $view    = 'foo.bar';
        $data    = [];

        $user->shouldReceive('getRecipientEmail')->once()->andReturn('hello@antaresplatform.com')
            ->shouldReceive('getRecipientName')->once()->andReturn('Administrator');
        $memory->shouldReceive('get')->once()->with('email.queue', false)->andReturn(true)
            ->shouldReceive('get')->once()->with('email.driver')->andReturn('mail');
        $message->shouldReceive('to')->once()->with('hello@antaresplatform.com', 'Administrator')->andReturnNull()
            ->shouldReceive('subject')->once()->with($subject)->andReturnNull();
        $mailer->shouldReceive('failures')->never();
        $notifier->shouldReceive('push')->once()->with($view, $data, m::type('Closure'))
                ->andReturnUsing(function ($v, $d, $c) use ($mailer, $message) {
                    $c($message);

                    return new Receipt($mailer, true);
                });

        $stub = new Antares($notifier);
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
        $mailer   = m::mock('\Illuminate\Contracts\Mail\Mailer');
        $message  = m::mock('\Illuminate\Mail\Message');
        $notifier = m::mock('\Antares\Notifier\Mailer')->makePartial();
        $user     = m::mock('\Antares\Contracts\Notification\Recipient');

        $subject = 'foobar';
        $view    = 'foo.bar';
        $data    = [];

        $user->shouldReceive('getRecipientEmail')->once()->andReturn('hello@antaresplatform.com')
            ->shouldReceive('getRecipientName')->once()->andReturn('Administrator');
        $message->shouldReceive('to')->once()->with('hello@antaresplatform.com', 'Administrator')->andReturnNull()
            ->shouldReceive('subject')->once()->with($subject)->andReturnNull();
        $mailer->shouldReceive('failures')->once()->andReturn(['hello@antaresplatform.com']);
        $notifier->shouldReceive('push')->once()->with($view, $data, m::type('Closure'))
                ->andReturnUsing(function ($v, $d, $c) use ($mailer, $message) {
                    $c($message);

                    return new Receipt($mailer, false);
                });

        $stub    = new Antares($notifier);
        $receipt = $stub->send($user, new Message(compact('subject', 'view', 'data')));

        $this->assertInstanceOf('\Antares\Notifier\Receipt', $receipt);
        $this->assertFalse($receipt->sent());
    }
}
