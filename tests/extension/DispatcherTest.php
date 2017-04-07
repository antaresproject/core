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

namespace Antares\Extension\TestCase;

use Antares\Extension\Collections\Extensions;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Dispatcher;
use Antares\Extension\Events\BootedAll;
use Antares\Extension\Loader;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Mockery as m;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Mockery\MockInterface
     */
    protected $container;

    /**
     * @var \Mockery\MockInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Mockery\MockInterface
     */
    protected $loader;

    public function setUp() {
        parent::setUp();

        $this->container        = m::mock(Container::class);
        $this->loader           = m::mock(Loader::class);
        $this->eventDispatcher  = m::mock(EventDispatcher::class);
    }

    public function tearDown() {
        parent::tearDown();
        m::close();
    }

    /**
     * @return Dispatcher
     */
    protected function getDispatcherInstance() {
        return new Dispatcher($this->container, $this->eventDispatcher, $this->loader);
    }

    public function testBootedMethodOnStart() {
        $this->assertFalse($this->getDispatcherInstance()->booted());
    }

    public function testRegisterCollectionAndBootMethods() {
        $extensions = [
            m::mock(ExtensionContract::class),
            m::mock(ExtensionContract::class),
            m::mock(ExtensionContract::class),
        ];

        foreach($extensions as $extension) {
            $this->loader
                ->shouldReceive('register')
                ->once()
                ->with($extension)
                ->andReturnNull()
                ->getMock();
        }

        $this->eventDispatcher
            ->shouldReceive('fire')
            ->times(4)
            ->andReturnNull()
            ->getMock();

        $dispatcher = $this->getDispatcherInstance();

        $dispatcher->registerCollection(new Extensions($extensions));

        $this->assertFalse($dispatcher->booted());

        $dispatcher->boot();

        $this->assertTrue($dispatcher->booted());
    }

    public function testRegisterAndBootMethods() {
        $extension = m::mock(ExtensionContract::class);

        $this->loader
            ->shouldReceive('register')
            ->once()
            ->with($extension)
            ->andReturnNull()
            ->getMock();

        $this->eventDispatcher
            ->shouldReceive('fire')
            ->times(2)
            ->andReturnNull()
            ->getMock();

        $dispatcher = $this->getDispatcherInstance();

        $dispatcher->register($extension);

        $this->assertFalse($dispatcher->booted());

        $dispatcher->boot();

        $this->assertTrue($dispatcher->booted());
    }

    public function testAfterMethodWithNullCallback() {
        $callback = null;

        $this->container
            ->shouldReceive('call')
            ->never()
            ->getMock();

        $this->eventDispatcher
            ->shouldReceive('listen')
            ->once()
            ->with(BootedAll::class, $callback)
            ->andReturnNull()
            ->getMock();

        $dispatcher = $this->getDispatcherInstance();
        $dispatcher->after($callback);
    }

    public function testAfterMethodWithNullCallbackOnBooted() {
        $callback = null;

        $this->container
            ->shouldReceive('call')
            ->never()
            ->getMock();

        $this->eventDispatcher
            ->shouldReceive('fire')
            ->once()
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('listen')
            ->once()
            ->with(BootedAll::class, $callback)
            ->andReturnNull()
            ->getMock();

        $dispatcher = $this->getDispatcherInstance();
        $dispatcher->boot();
        $dispatcher->after($callback);
    }

    public function testAfterMethodWithCallback() {
        $callback = function() {};

        $this->container
            ->shouldReceive('call')
            ->never()
            ->getMock();

        $this->eventDispatcher
            ->shouldReceive('listen')
            ->once()
            ->with(BootedAll::class, $callback)
            ->andReturnNull()
            ->getMock();

        $dispatcher = $this->getDispatcherInstance();
        $dispatcher->after($callback);
    }

    public function testAfterMethodWithCallbackOnBooted() {
        $callback = function() {};

        $this->container
            ->shouldReceive('call')
            ->once()
            ->with($callback)
            ->getMock();

        $this->eventDispatcher
            ->shouldReceive('fire')
            ->once()
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('listen')
            ->once()
            ->with(BootedAll::class, $callback)
            ->andReturnNull()
            ->getMock();

        $dispatcher = $this->getDispatcherInstance();
        $dispatcher->boot();
        $dispatcher->after($callback);
    }

}