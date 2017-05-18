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

//use Antares\Testbench\ApplicationTestCase;
use Mockery as m;
use Antares\Notifier\Mailer;
use SuperClosure\SerializableClosure;
use Antares\Notifier\TransportManager;
use Antares\Testing\ApplicationTestCase;

class MailerTest extends ApplicationTestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
//
        $memory = m::mock('\Antares\Contracts\Memory\Provider');
        $memory->shouldReceive('get')->with('email', [])->andReturn(['driver' => 'mail'])
                ->shouldReceive('get')->with('email.driver', 'mail')->andReturn('mail')
                ->shouldReceive('get')->with('email.from')->andReturn([
            'address' => 'hello@antaresplatform.com',
            'name'    => 'Antares Platform',
        ]);

        $this->app['antares.memory'] = $memory;
        $transport                   = new TransportManager($this->app);
        $mailer                      = with(new Mailer($this->app, $transport))->attach($memory);
        $this->app['antares.mail']   = $mailer;
    }

    /**
     * Test Antares\Notifier\Mailer::push() method uses Mail::send().
     *
     * @test
     */
    public function testPushMethodUsesSend()
    {
        $app    = $this->app;
        $memory = $app['antares.memory'];
        $memory->shouldReceive('get')->twice()->with('email.queue', false)->andReturn(false);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);

        $this->assertInstanceOf('\Antares\Notifier\Mailer', $stub->push('foo.bar', ['foo' => 'foobar'], ''));
    }

    /**
     * Test Antares\Notifier\Mailer::push() method uses Mail::queue().
     *
     * @test
     */
    public function testPushMethodUsesQueue()
    {
        $app          = $this->app;
        $memory       = $app['antares.memory'];
        $app['queue'] = $queue        = m::mock('QueueListener');

        $with = [
            'view'     => 'foo.bar',
            'data'     => ['foo' => 'foobar'],
            'callback' => function () {
                
            },
        ];


        $memory->shouldReceive('get')->once()->with('email.queue', false)->andReturn(true);
        $queue->shouldReceive('push')->once()
                ->with('antares.mail@handleQueuedMessage', m::type('Array'), m::any())->andReturn(true);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->push($with['view'], $with['data'], $with['callback']));
    }

    /**
     * Test Antares\Notifier\Mailer::send() method.
     *
     * @test
     */
    public function testSendMethod()
    {
        $app       = $this->app;
        $mailer    = $app['mailer'];
        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Mailer', $stub->send('foo.bar', ['foo' => 'foobar'], ''));
    }

    /**
     * Test Antares\Notifier\Mailer::send() method using smtp.
     *
     * @test
     */
    public function testSendMethodViaSmtp()
    {
        $memory = $this->app['antares.memory'];
        $mailer = $this->app['mailer'];


        $memory->shouldReceive('get')->with('email.driver', 'mail')->andReturn('smtp')
                ->shouldReceive('get')->with('email', [])->andReturn([
                    'driver'     => 'smtp',
                    'host'       => 'smtp.mailgun.org',
                    'port'       => 587,
                    'encryption' => 'tls',
                    'username'   => 'hello@antaresplatform.com',
                    'password'   => 123456,
                ])
                ->shouldReceive('get')->with('email.from')->andReturn([
            'address' => 'hello@antaresplatform.com',
            'name'    => 'Antares Platform',
        ]);


        $this->app['mailer']         = $mailer;
        $this->app['antares.memory'] = $memory;

        $this->app['antares.support.mail'] = $mailer                            = m::mock('\Antares\Notifier\Mail\Mailer');
        $mailer->shouldReceive('setSwiftMailer')->once()->andReturn(null)
                ->shouldReceive('alwaysFrom')->once()->with('hello@antaresplatform.com', 'Antares Platform')
                ->shouldReceive('send')->once()->with('foo.bar', ['foo' => 'foobar'], '')->andReturn(true);
        $transport                         = new TransportManager($this->app);

        $stub = with(new Mailer($this->app, $transport))->attach($this->app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Mailer', $stub->send('foo.bar', ['foo' => 'foobar'], ''));
    }

    /**
     * Test Antares\Notifier\Mailer::send() method using invalid driver
     * throws exception.
     *
     */
    public function testSendMethodViaInvalidDriverThrowsException()
    {
        $transport = new TransportManager($this->app);
        $stub      = with(new Mailer($this->app, $transport))->attach($this->app['antares.memory']);
        $stub->send('foo.bar', ['foo' => 'foobar'], '');
    }

    /**
     * Test Antares\Notifier\Mailer::queue() method.
     *
     * @test
     */
    public function testQueueMethod()
    {
        $app          = $this->app;
        $app['queue'] = $queue        = m::mock('QueueListener');

        $with = [
            'view'     => 'foo.bar',
            'data'     => ['foo' => 'foobar'],
            'callback' => function () {
                
            },
        ];

        $queue->shouldReceive('push')->once()
                ->with('antares.mail@handleQueuedMessage', m::type('Array'), m::any())->andReturn(true);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->queue($with['view'], $with['data'], $with['callback']));
    }

    /**
     * Test Antares\Notifier\Mailer::queue() method when a class name
     * is given.
     *
     * @test
     */
    public function testQueueMethodWhenClassNameIsGiven()
    {
        $app          = $this->app;
        $app['queue'] = $queue        = m::mock('QueueListener');

        $with = [
            'view'     => 'foo.bar',
            'data'     => ['foo' => 'foobar'],
            'callback' => 'FooMailHandler@foo',
        ];

        $queue->shouldReceive('push')->once()
                ->with('antares.mail@handleQueuedMessage', $with, '')
                ->andReturn(true);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->queue($with['view'], $with['data'], $with['callback']));
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function queueMessageDataProvdier()
    {
        $callback = new SerializableClosure(function () {
            
        });

        return [
            [
                'view'     => 'foo.bar',
                'data'     => ['foo' => 'foobar'],
                'callback' => serialize($callback),
            ],
            [
                'view'     => 'foo.bar',
                'data'     => ['foo' => 'foobar'],
                'callback' => "hello world",
            ],
        ];
    }

    /**
     * Test Antares\Notifier\Mailer::handleQueuedMessage() method.
     *
     * @test
     * @dataProvider queueMessageDataProvdier
     */
    public function testHandleQueuedMessageMethod($view, $data, $callback)
    {
        $app = $this->app;
        $job = m::mock('\Illuminate\Contracts\Queue\Job');

        $job->shouldReceive('delete')->once()->andReturn(null);
        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);

        $stub->handleQueuedMessage($job, compact('view', 'data', 'callback'));
    }

}
