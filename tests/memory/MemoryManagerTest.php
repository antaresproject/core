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

namespace Antares\Memory\TestCase;

use Mockery as m;
use Antares\Memory\Handler;
use Antares\Memory\Provider;
use Antares\Memory\MemoryManager;
use Antares\Contracts\Memory\Handler as HandlerContract;
use Antares\Testing\TestCase;

class MemoryManagerTest extends TestCase
{

    /**
     * Application mock instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = m::mock('\Illuminate\Container\Container');
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
//        parent::t
//        unset($this->app);
//        m::close();
    }

    /**
     * Test that Antares\Memory\MemoryManager::make() return an instanceof
     * Antares\Memory\MemoryManager.
     *
     * @test
     */
    public function testMakeMethod()
    {
        $app = $this->app;

        $cache    = m::mock('\Illuminate\Contracts\Cache\Repository');
        $db       = m::mock('\Illuminate\Database\DatabaseManager');
        $eloquent = m::mock('EloquentHandlerModelMock');

        $app->shouldReceive('offsetGet')->with('cache')->andReturn($cache)
                ->shouldReceive('offsetGet')->with('db')->andReturn($db);

        $cache->shouldReceive('driver')->with(null)->andReturnSelf()
                ->shouldReceive('get')->andReturn([])
                ->shouldReceive('rememberForever')->andReturn([])
                ->shouldReceive('forever')->andReturn(true);

        $config = [
            'fluent'   => [
                'default' => ['table' => 'antares_options', 'cache' => true],
            ],
            'eloquent' => [
                'default' => ['model' => $eloquent, 'cache' => true],
            ],
        ];

        $stub = new MemoryManager($app);
        $stub->setConfig($config);
    }

    /**
     * Test that Antares\Memory\MemoryManager::makeOrFallback() method.
     *
     * @test
     */
    public function testMakeOrFallbackMethodReturnFluent()
    {
        $app = $this->app;

        $cache = m::mock('\Illuminate\Contracts\Cache\Repository');
        $db    = m::mock('\Illuminate\Database\DatabaseManager');
        $query = m::mock('\Illuminate\Database\Query\Builder');
        $data  = [];

        $app->shouldReceive('offsetGet')->with('cache')->andReturn($cache)
                ->shouldReceive('offsetGet')->with('db')->andReturn($db);

        $cache->shouldReceive('driver')->with(null)->andReturnSelf()
                ->shouldReceive('rememberForever')->with('db-memory:fluent-default', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) {
                    return $c();
                });

        $config = [
            'driver' => 'fluent.default',
            'fluent' => [
                'default' => ['table' => 'antares_options', 'cache' => true],
            ],
        ];

        $db->shouldReceive('table')->with('antares_options')->andReturn($query);
        $query->shouldReceive('get')->andReturn($data);

        $stub = new MemoryManager($app);
        $stub->setConfig($config);
    }

    /**
     * Test that Antares\Memory\MemoryManager::makeOrFallback() method.
     *
     * @test
     */
    public function testMakeOrFallbackMethodReturnRuntime()
    {
        $app = $this->app;

        $cache = m::mock('\Illuminate\Contracts\Cache\Repository');
        $db    = m::mock('\Illuminate\Database\DatabaseManager');

        $app->shouldReceive('offsetGet')->with('cache')->andReturn($cache)
                ->shouldReceive('offsetGet')->with('db')->andReturn($db);

        $cache->shouldReceive('driver')->with('foo')->andReturnSelf()
                ->shouldReceive('rememberForever')->with('db-memory:fluent-default', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) {
                    return $c();
                });

        $config = [
            'driver'  => 'fluent.default',
            'fluent'  => [
                'default' => ['table' => 'antares_options', 'cache' => true, 'connections' => ['cache' => 'foo']],
            ],
            'runtime' => [
                'antares' => [],
            ],
        ];

        $db->shouldReceive('table')->with('antares_options')->andThrow('Exception');

        $stub = new MemoryManager($app);
        $stub->setConfig($config);
    }

    /**
     * Test that Antares\Memory\MemoryManager::make() return exception when given invalid driver.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMakeExpectedException()
    {
        with(new MemoryManager($this->app))->make('orm');
    }

    /**
     * Test Antares\Memory\MemoryManager::extend() return valid Memory instance.
     *
     * @test
     */
    public function testStubMemory()
    {
        $app = $this->app;

        $stub = new MemoryManager($app);

        $stub->extend('stub', function ($app, $name) {
            $handler = new StubMemoryHandler($name, []);

            return new Provider($handler);
        });

        $stub = $stub->make('stub.mock');

        $this->assertInstanceOf('\Antares\Memory\Provider', $stub);

        $refl    = new \ReflectionObject($stub);
        $handler = $refl->getProperty('handler');

        $handler->setAccessible(true);

        $this->assertInstanceOf('\Antares\Contracts\Memory\Handler', $handler->getValue($stub));
    }

    /**
     * Test Antares\Memory\MemoryManager::finish() method.
     *
     * @test
     */
    public function testFinishMethod()
    {
        $app = $this->app;

        $stub = new MemoryManager($app);
        $foo  = $stub->make('runtime.fool');

        $this->assertTrue($foo === $stub->make('runtime.fool'));

        $stub->finish();

        $this->assertFalse($foo === $stub->make('runtime.fool'));
    }

    /**
     * Test that Antares\Memory\MemoryManager::make() default driver.
     *
     * @test
     */
    public function testMakeMethodForDefaultDriver()
    {
        $app    = $this->app;
        $config = ['driver' => 'runtime.default'];

        $stub = new MemoryManager($app);
        $stub->setConfig($config);
        $stub->make();
    }

    /**
     * Test Antares\Memory\MemoryManager::setDefaultDriver() method.
     *
     * @rest
     */
    public function testSetDefaultDriverMethod()
    {
        $app = $this->app;

        $stub = new MemoryManager($app);
        $stub->setDefaultDriver('foo');

        $this->assertEquals(['driver' => 'foo'], $stub->getConfig());
    }

}

class StubMemoryHandler extends Handler implements HandlerContract
{

    protected $storage = 'stub';

    public function initiate()
    {
        return [];
    }

    public function finish(array $items = [])
    {
        return true;
    }

}
