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

use Mockery as m;
use Antares\Notifier\Message;
use Antares\Notifier\Handlers\Laravel;

class LaravelTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Notifier\LaravelNotifier::send() method without
     * queue.
     *
     * @test
     */
    public function testSendMethodSucceed()
    {
        $mailer  = m::mock('\Illuminate\Contracts\Mail\Mailer');
        $message = m::mock('\Illuminate\Mail\Message');
        $user    = m::mock('\Antares\Contracts\Notification\Recipient');

        $subject = 'foobar';
        $view    = 'foo.bar';
        $data    = [];

        $user->shouldReceive('getRecipientEmail')->once()->andReturn('hello@antaresplatform.com')
                ->shouldReceive('getRecipientName')->once()->andReturn('Administrator');
        $mailer->shouldReceive('send')->once()->with($view, $data, m::type('Closure'))
                ->andReturnUsing(function ($v, $d, $c) use ($mailer, $message) {
                    $c($message);

                    return $mailer;
                })
                ->shouldReceive('failures')->once()->andReturn([]);
        $message->shouldReceive('to')->once()->with('hello@antaresplatform.com', 'Administrator')->andReturnNull()
                ->shouldReceive('subject')->once()->with($subject)->andReturnNull();

        $stub    = new Laravel($mailer);
        $receipt = $stub->send($user, new Message(compact('subject', 'view', 'data')));

        $this->assertInstanceOf('\Antares\Notifier\Receipt', $receipt);
        $this->assertTrue($receipt->sent());
    }

    /**
     * Test Antares\Notifier\LaravelNotifier::send() method with callback.
     *
     * @test
     */
    public function testSendMethodWithCallback()
    {
        $mailer  = m::mock('\Illuminate\Contracts\Mail\Mailer');
        $message = m::mock('\Illuminate\Mail\Message');
        $user    = m::mock('\Antares\Contracts\Notification\Recipient');

        $subject = 'foobar';
        $view    = 'foo.bar';
        $data    = [];

        $callback = function ($mail) {
            $mail->subject('foobar!!');
        };

        $user->shouldReceive('getRecipientEmail')->once()->andReturn('hello@antaresplatform.com')
                ->shouldReceive('getRecipientName')->once()->andReturn('Administrator');

        $mailer->shouldReceive('send')->once()->with($view, $data, m::type('Closure'))
                ->andReturnUsing(function ($v, $d, $c) use ($mailer, $message) {
                    $c($message);

                    return $mailer;
                })
                ->shouldReceive('failures')->once()->andReturn([]);
        $message->shouldReceive('to')->once()->with('hello@antaresplatform.com', 'Administrator')->andReturnNull()
                ->shouldReceive('subject')->once()->with($subject)->andReturnNull()
                ->shouldReceive('subject')->once()->with('foobar!!')->andReturnNull();

        $stub = new Laravel($mailer);

        $receipt = $stub->send($user, new Message(compact('subject', 'view', 'data')), $callback);

        $this->assertInstanceOf('\Antares\Notifier\Receipt', $receipt);
        $this->assertTrue($receipt->sent());
    }

    /**
     * Test Antares\Notifier\LaravelNotifier::send() method failed.
     *
     * @test
     */
    public function testSendMethodFailed()
    {
        $mailer  = m::mock('\Illuminate\Contracts\Mail\Mailer');
        $message = m::mock('\Illuminate\Mail\Message');
        $user    = m::mock('\Antares\Contracts\Notification\Recipient');

        $subject = 'foobar';
        $view    = 'foo.bar';
        $data    = [];

        $user->shouldReceive('getRecipientEmail')->once()->andReturn('hello@antaresplatform.com')
                ->shouldReceive('getRecipientName')->once()->andReturn('Administrator');
        $mailer->shouldReceive('send')->once()->with($view, $data, m::type('Closure'))
                ->andReturnUsing(function ($v, $d, $c) use ($mailer, $message) {
                    $c($message);

                    return $mailer;
                })
                ->shouldReceive('failures')->once()->andReturn(['hello@antaresplatform.com']);
        $message->shouldReceive('to')->once()->with('hello@antaresplatform.com', 'Administrator')->andReturnNull()
                ->shouldReceive('subject')->once()->with($subject)->andReturnNull();

        $stub    = new Laravel($mailer);
        $receipt = $stub->send($user, new Message(compact('subject', 'view', 'data')));

        $this->assertInstanceOf('\Antares\Notifier\Receipt', $receipt);
        $this->assertFalse($receipt->sent());
    }

}
