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
 namespace Antares\Notifier\TestCase;

use Mockery as m;
use Antares\Notifier\Mailer;
use Illuminate\Container\Container;
use SuperClosure\SerializableClosure;
use Antares\Notifier\TransportManager;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    private $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();

        $memory = m::mock('\Antares\Contracts\Memory\Provider');

        $memory->shouldReceive('get')->with('email', [])->andReturn(['driver' => 'mail'])
            ->shouldReceive('get')->with('email.driver', 'mail')->andReturn('mail')
            ->shouldReceive('get')->with('email.from')->andReturn([
                'address' => 'hello@antaresplatform.com',
                'name'    => 'Antares Platform',
            ]);

        $this->app['antares.memory'] = $memory;
        $this->app['mailer']           = m::mock('\Illuminate\Contracts\Mail\Mailer');
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        m::close();
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
        $mailer = $app['mailer'];

        $memory->shouldReceive('get')->twice()->with('email.queue', false)->andReturn(false);
        $mailer->shouldReceive('setSwiftMailer')->once()->andReturn(null)
            ->shouldReceive('alwaysFrom')->once()->with('hello@antaresplatform.com', 'Antares Platform')
            ->shouldReceive('send')->twice()->with('foo.bar', ['foo' => 'foobar'], '')->andReturn(true);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);

        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->push('foo.bar', ['foo' => 'foobar'], ''));
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->push('foo.bar', ['foo' => 'foobar'], ''));
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
        $app['queue'] = $queue = m::mock('QueueListener');

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
        $app    = $this->app;
        $mailer = $app['mailer'];

        $mailer->shouldReceive('setSwiftMailer')->once()->andReturn(null)
            ->shouldReceive('alwaysFrom')->once()->with('hello@antaresplatform.com', 'Antares Platform')
            ->shouldReceive('send')->once()->with('foo.bar', ['foo' => 'foobar'], '')->andReturn(true);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->send('foo.bar', ['foo' => 'foobar'], ''));
    }

    /**
     * Test Antares\Notifier\Mailer::send() method using mail.
     *
     * @test
     */
    public function testSendMethodViaMail()
    {
        $app = [
            'antares.memory' => $memory = m::mock('\Antares\Contracts\Memory\Provider'),
            'mailer'           => $mailer = m::mock('\Illuminate\Contracts\Mail\Mailer'),
        ];

        $memory->shouldReceive('get')->with('email.driver', 'mail')->andReturn('mail')
            ->shouldReceive('get')->with('email.from')->andReturn([
                'address' => 'hello@antaresplatform.com',
                'name'    => 'Antares Platform',
            ]);

        $mailer->shouldReceive('setSwiftMailer')->once()->andReturn(null)
            ->shouldReceive('alwaysFrom')->once()->with('hello@antaresplatform.com', 'Antares Platform')
            ->shouldReceive('send')->once()->with('foo.bar', ['foo' => 'foobar'], '')->andReturn(true);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->send('foo.bar', ['foo' => 'foobar'], ''));
    }

    /**
     * Test Antares\Notifier\Mailer::send() method using sendmail.
     *
     * @test
     */
    public function testSendMethodViaSendMail()
    {
        $app = [
            'antares.memory' => $memory = m::mock('\Antares\Contracts\Memory\Provider'),
            'mailer'           => $mailer = m::mock('\Illuminate\Contracts\Mail\Mailer'),
        ];

        $memory->shouldReceive('get')->with('email.driver', 'mail')->andReturn('sendmail')
            ->shouldReceive('get')->with('email', [])->andReturn([
                'driver'   => 'sendmail',
                'sendmail' => '/bin/sendmail -t',
            ])
            ->shouldReceive('get')->with('email.from')->andReturn([
                'address' => 'hello@antaresplatform.com',
                'name'    => 'Antares Platform',
            ]);

        $mailer->shouldReceive('setSwiftMailer')->once()->andReturn(null)
            ->shouldReceive('alwaysFrom')->once()->with('hello@antaresplatform.com', 'Antares Platform')
            ->shouldReceive('send')->once()->with('foo.bar', ['foo' => 'foobar'], '')->andReturn(true);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->send('foo.bar', ['foo' => 'foobar'], ''));
    }

    /**
     * Test Antares\Notifier\Mailer::send() method using smtp.
     *
     * @test
     */
    public function testSendMethodViaSmtp()
    {
        $app = [
            'antares.memory' => $memory = m::mock('\Antares\Contracts\Memory\Provider'),
            'mailer'           => $mailer = m::mock('\Illuminate\Contracts\Mail\Mailer'),
        ];

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

        $mailer->shouldReceive('setSwiftMailer')->once()->andReturn(null)
            ->shouldReceive('alwaysFrom')->once()->with('hello@antaresplatform.com', 'Antares Platform')
            ->shouldReceive('send')->once()->with('foo.bar', ['foo' => 'foobar'], '')->andReturn(true);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->send('foo.bar', ['foo' => 'foobar'], ''));
    }

    /**
     * Test Antares\Notifier\Mailer::send() method using mailgun.
     *
     * @test
     */
    public function testSendMethodViaMailgun()
    {
        $app = [
            'antares.memory' => $memory = m::mock('\Antares\Contracts\Memory\Provider'),
            'mailer'           => $mailer = m::mock('\Illuminate\Contracts\Mail\Mailer'),
        ];

        $memory->shouldReceive('get')->with('email.driver', 'mail')->andReturn('mailgun')
            ->shouldReceive('get')->with('email', [])->andReturn([
                'driver' => 'mailgun',
                'secret' => 'auniquetoken',
                'domain' => 'mailer.mailgun.org',
            ])
            ->shouldReceive('get')->with('email.from')->andReturn([
                'address' => 'hello@antaresplatform.com',
                'name'    => 'Antares Platform',
            ]);

        $mailer->shouldReceive('setSwiftMailer')->once()->andReturn(null)
            ->shouldReceive('alwaysFrom')->once()->with('hello@antaresplatform.com', 'Antares Platform')
            ->shouldReceive('send')->once()->with('foo.bar', ['foo' => 'foobar'], '')->andReturn(true);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->send('foo.bar', ['foo' => 'foobar'], ''));
    }

    /**
     * Test Antares\Notifier\Mailer::send() method using mandrill.
     *
     * @test
     */
    public function testSendMethodViaMandrill()
    {
        $app = [
            'antares.memory' => $memory = m::mock('\Antares\Contracts\Memory\Provider'),
            'mailer'           => $mailer = m::mock('\Illuminate\Contracts\Mail\Mailer'),
        ];

        $memory->shouldReceive('get')->with('email.driver', 'mail')->andReturn('mandrill')
            ->shouldReceive('get')->with('email', [])->andReturn([
                'driver' => 'mandrill',
                'secret' => 'auniquetoken',
            ])
            ->shouldReceive('get')->with('email.from')->andReturn([
                'address' => 'hello@antaresplatform.com',
                'name'    => 'Antares Platform',
            ]);

        $mailer->shouldReceive('setSwiftMailer')->once()->andReturn(null)
            ->shouldReceive('alwaysFrom')->once()->with('hello@antaresplatform.com', 'Antares Platform')
            ->shouldReceive('send')->once()->with('foo.bar', ['foo' => 'foobar'], '')->andReturn(true);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->send('foo.bar', ['foo' => 'foobar'], ''));
    }

    /**
     * Test Antares\Notifier\Mailer::send() method using log.
     *
     * @test
     */
    public function testSendMethodViaLog()
    {
        $monolog = m::mock('\Psr\Log\LoggerInterface');

        $app = [
            'antares.memory' => $memory = m::mock('\Antares\Contracts\Memory\Provider'),
            'mailer'           => $mailer = m::mock('\Illuminate\Contracts\Mail\Mailer'),
            'log'              => $logger = m::mock('\Illuminate\Log\Writer'),
        ];

        $memory->shouldReceive('get')->with('email.driver', 'mail')->andReturn('log')
            ->shouldReceive('get')->with('email.from')->andReturn([
                'address' => 'hello@antaresplatform.com',
                'name'    => 'Antares Platform',
            ]);

        $mailer->shouldReceive('setSwiftMailer')->once()->andReturn(null)
            ->shouldReceive('alwaysFrom')->once()->with('hello@antaresplatform.com', 'Antares Platform')
            ->shouldReceive('send')->once()->with('foo.bar', ['foo' => 'foobar'], '')->andReturn(true);

        $logger->shouldReceive('getMonolog')->once()->andReturn($monolog);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $this->assertInstanceOf('\Antares\Notifier\Receipt', $stub->send('foo.bar', ['foo' => 'foobar'], ''));
    }

    /**
     * Test Antares\Notifier\Mailer::send() method using invalid driver
     * throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSendMethodViaInvalidDriverThrowsException()
    {
        $app = [
            'antares.memory' => $memory = m::mock('\Antares\Contracts\Memory\Provider'),
            'mailer'           => $mailer = m::mock('\Illuminate\Contracts\Mail\Mailer'),
        ];

        $memory->shouldReceive('get')->once()
                ->with('email.driver', 'mail')->andReturn('invalid-driver')
            ->shouldReceive('get')->once()
                ->with('email.from')
                ->andReturn([
                    'address' => 'hello@antaresplatform.com',
                    'name'    => 'Antares Platform',
                ]);

        $mailer->shouldReceive('alwaysFrom')->once()
            ->with('hello@antaresplatform.com', 'Antares Platform')
            ->andReturnNull();

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
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
        $app['queue'] = $queue = m::mock('QueueListener');

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
        $app['queue'] = $queue = m::mock('QueueListener');

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
        $app    = $this->app;
        $mailer = $app['mailer'];
        $job    = m::mock('\Illuminate\Contracts\Queue\Job');

        $job->shouldReceive('delete')->once()->andReturn(null);
        $mailer->shouldReceive('setSwiftMailer')->once()->andReturn(null)
            ->shouldReceive('alwaysFrom')->once()->with('hello@antaresplatform.com', 'Antares Platform')
            ->shouldReceive('send')->once()
                ->with($view, $data, m::any())->andReturn(true);

        $transport = new TransportManager($app);
        $stub      = with(new Mailer($app, $transport))->attach($app['antares.memory']);
        $stub->handleQueuedMessage($job, compact('view', 'data', 'callback'));
    }
}
