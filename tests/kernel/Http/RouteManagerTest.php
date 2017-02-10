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
 namespace Antares\Http\TestCase;

use Mockery as m;
use Antares\Http\RouteManager;
use Illuminate\Support\Facades\Facade;

class RouteManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app                            = m::mock('\Illuminate\Contracts\Foundation\Application', '\ArrayAccess');
        $_SERVER['RouteManagerTest@callback'] = null;

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this->app);
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        unset($_SERVER['RouteManagerTest@callback']);

        m::close();
    }

    /**
     * Installed setup.
     */
    private function getApplicationMocks()
    {
        $app = $this->app;
        $app->shouldReceive('offsetGet')->with('request')->andReturn($request = m::mock('\Illuminate\Http\Request'));

        $request->shouldReceive('root')->andReturn('http://localhost')
            ->shouldReceive('secure')->andReturn(false);

        return $app;
    }

    /**
     * Test Antares\Http\RouteManager::group() method.
     *
     * @test
     */
    public function testGroupMethod()
    {
        $app       = $this->getApplicationMocks();
        $extension = m::mock('\Antares\Contracts\Extension\Factory');
        $appRoute  = m::mock('\Antares\Contracts\Extension\RouteGenerator');

        $app->shouldReceive('offsetGet')->with('antares.extension')->andReturn($extension);

        $extension->shouldReceive('route')->once()
            ->with('admin', 'admin')->andReturn($appRoute);

        $appRoute->shouldReceive('prefix')->once()->andReturn('admin')
            ->shouldReceive('domain')->once()->andReturnNull();

        $stub = new StubRouteManager($app);

        $expected = [
            'before' => 'auth',
            'prefix' => 'admin',
            'domain' => null,
        ];

        $this->assertEquals($expected, $stub->group('admin', 'admin', ['before' => 'auth']));
    }

    /**
     * Test Antares\Http\RouteManager::group() method
     * with closure.
     *
     * @test
     */
    public function testGroupMethodWithClosure()
    {
        $app       = $this->getApplicationMocks();
        $extension = m::mock('\Antares\Contracts\Extension\Factory');
        $router    = m::mock('\Illuminate\Routing\Router');
        $appRoute  = m::mock('\Antares\Contracts\Extension\RouteGenerator');

        $app->shouldReceive('offsetGet')->with('antares.extension')->andReturn($extension)
            ->shouldReceive('offsetGet')->with('router')->andReturn($router);

        $extension->shouldReceive('route')->once()->with('admin', 'admin')->andReturn($appRoute);

        $appRoute->shouldReceive('prefix')->once()->andReturn('admin')
            ->shouldReceive('domain')->once()->andReturnNull();

        $group = [
            'before' => 'auth',
            'prefix' => 'admin',
            'domain' => null,
        ];

        $callback = function () { };

        $router->shouldReceive('group')->once()->with($group, $callback)->andReturnNull();

        $stub = new StubRouteManager($app);

        $this->assertEquals($group, $stub->group('admin', 'admin', ['before' => 'auth'], $callback));
    }

    /**
     * Test Antares\Http\RouteManager::group() method
     * with closure and not array.
     *
     * @test
     */
    public function testGroupMethodWithClosureAndNotArray()
    {
        $app       = $this->getApplicationMocks();
        $extension = m::mock('\Antares\Contracts\Extension\Factory');
        $router    = m::mock('\Illuminate\Routing\Router');
        $appRoute  = m::mock('\Antares\Contracts\Extension\RouteGenerator');

        $app->shouldReceive('offsetGet')->with('antares.extension')->andReturn($extension)
            ->shouldReceive('offsetGet')->with('router')->andReturn($router);

        $extension->shouldReceive('route')->once()->with('admin', 'admin')->andReturn($appRoute);

        $appRoute->shouldReceive('prefix')->once()->andReturn('admin')
            ->shouldReceive('domain')->once()->andReturnNull();

        $group = [
            'prefix' => 'admin',
            'domain' => null,
        ];

        $callback = function () { };

        $router->shouldReceive('group')->once()->with($group, $callback)->andReturnNull();

        $stub = new StubRouteManager($app);

        $this->assertEquals($group, $stub->group('admin', 'admin', $callback));
    }

    /**
     * Test Antares\Http\RouteManager::handles() method.
     *
     * @test
     */
    public function testHandlesMethod()
    {
        $app       = $this->getApplicationMocks();
        $config    = m::mock('\Illuminate\Contracts\Config\Repository');
        $extension = m::mock('\Antares\Contracts\Extension\Factory');
        $url       = m::mock('\Illuminate\Routing\UrlGenerator');

        $app->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('offsetGet')->with('antares.extension')->andReturn($extension)
            ->shouldReceive('offsetGet')->with('url')->andReturn($url);

        $appRoute = m::mock('\Antares\Contracts\Extension\RouteGenerator');

        $appRoute->shouldReceive('to')->once()->with('/')->andReturn('/')
            ->shouldReceive('to')->once()->with('info?foo=bar')->andReturn('info?foo=bar');
        $extension->shouldReceive('route')->once()->with('app', '/')->andReturn($appRoute);
        $url->shouldReceive('isValidUrl')->with('app::/')->andReturn(false)
            ->shouldReceive('isValidUrl')->once()->with('info?foo=bar')->andReturn(false)
            ->shouldReceive('isValidUrl')->once()->with('http://localhost/admin')->andReturn(true)
            ->shouldReceive('to')->once()->with('/')->andReturn('/')
            ->shouldReceive('to')->once()->with('info?foo=bar')->andReturn('info?foo=bar');

        $stub = new StubRouteManager($app);

        $this->assertEquals('/', $stub->handles('app::/'));
        $this->assertEquals('info?foo=bar', $stub->handles('info?foo=bar'));
        $this->assertEquals('http://localhost/admin', $stub->handles('http://localhost/admin'));
    }

    /**
     * Test Antares\Http\RouteManager::handles() method
     * with CSRF Token.
     *
     * @test
     */
    public function testHandlesMethodWithCsrfToken()
    {
        $app       = $this->getApplicationMocks();
        $config    = m::mock('\Illuminate\Contracts\Config\Repository');
        $extension = m::mock('\Antares\Contracts\Extension\Factory');
        $session   = m::mock('\Illuminate\Session\Store');
        $url       = m::mock('\Illuminate\Routing\UrlGenerator');

        $app->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('offsetGet')->with('antares.extension')->andReturn($extension)
            ->shouldReceive('offsetGet')->with('session')->andReturn($session)
            ->shouldReceive('offsetGet')->with('url')->andReturn($url);

        $appRoute = m::mock('\Antares\Contracts\Extension\RouteGenerator');

        $appRoute->shouldReceive('to')->once()->with('/?_token=StAGiQ')->andReturn('/?_token=StAGiQ')
            ->shouldReceive('to')->once()->with('info?foo=bar&_token=StAGiQ')->andReturn('info?foo=bar&_token=StAGiQ');
        $extension->shouldReceive('route')->once()->with('app', '/')->andReturn($appRoute);
        $session->shouldReceive('getToken')->twice()->andReturn('StAGiQ');
        $url->shouldReceive('isValidUrl')->once()->with('app::/')->andReturn(false)
            ->shouldReceive('isValidUrl')->once()->with('info?foo=bar')->andReturn(false)
            ->shouldReceive('to')->once()->with('/?_token=StAGiQ')->andReturn('/?_token=StAGiQ')
            ->shouldReceive('to')->once()->with('info?foo=bar&_token=StAGiQ')->andReturn('info?foo=bar&_token=StAGiQ');

        $stub = new StubRouteManager($app);

        $options = ['csrf' => true];

        $this->assertEquals('/?_token=StAGiQ', $stub->handles('app::/', $options));
        $this->assertEquals('info?foo=bar&_token=StAGiQ', $stub->handles('info?foo=bar', $options));
    }

    /**
     * Test Antares\Http\RouteManager::is() method.
     *
     * @test
     */
    public function testIsMethod()
    {
        $app       = $this->getApplicationMocks();
        $request   = $app['request'];
        $config    = m::mock('\Illuminate\Config\Repository');
        $extension = m::mock('\Antares\Extension\Factory');
        $url       = m::mock('\Illuminate\Routing\UrlGenerator');

        $app->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('offsetGet')->with('antares.extension')->andReturn($extension)
            ->shouldReceive('offsetGet')->with('url')->andReturn($url);

        $appRoute = m::mock('\Antares\Contracts\Extension\RouteGenerator');

        $request->shouldReceive('path')->never()->andReturn('/');
        $appRoute->shouldReceive('is')->once()->with('/')->andReturn(true)
            ->shouldReceive('is')->once()->with('info?foo=bar')->andReturn(true);
        $extension->shouldReceive('route')->once()->with('app', '/')->andReturn($appRoute);

        $stub = new StubRouteManager($app);

        $this->assertTrue($stub->is('app::/'));
        $this->assertTrue($stub->is('info?foo=bar'));
    }

    /**
     * Test Antares\Http\RouteManager::when() method.
     *
     * @test
     */
    public function testWhenMethod()
    {
        $app       = $this->getApplicationMocks();
        $config    = m::mock('\Illuminate\Config\Repository');
        $events    = m::mock('\Illuminate\Events\Dispatcher');
        $extension = m::mock('\Antares\Extension\Factory');
        $url       = m::mock('\Illuminate\Routing\UrlGenerator');

        $app->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('antares.extension')->andReturn($extension)
            ->shouldReceive('offsetGet')->with('url')->andReturn($url)
            ->shouldReceive('boot')->andReturnNull()
            ->shouldReceive('booted')->twice()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) {
                    return $c();
                });

        $appRoute = m::mock('\Antares\Extension\RouteGenerator');

        $appRoute->shouldReceive('is')->once()->with('/')->andReturn(true)
            ->shouldReceive('is')->once()->with('foo')->andReturn(false);
        $extension->shouldReceive('route')->once()->with('app', '/')->andReturn($appRoute);
        $events->shouldReceive('makeListener')->twice()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) {
                    return $c;
                });

        $stub = new StubRouteManager($app);

        $this->assertNull($_SERVER['RouteManagerTest@callback']);

        $stub->when('app::/', function () {
            $_SERVER['RouteManagerTest@callback'] = 'app::/';
        });

        $app->boot();

        $this->assertEquals('app::/', $_SERVER['RouteManagerTest@callback']);

        $stub->when('app::foo', function () {
            $_SERVER['RouteManagerTest@callback'] = 'app::foo';
        });

        $app->boot();

        $this->assertNotEquals('app::foo', $_SERVER['RouteManagerTest@callback']);
    }
}

class StubRouteManager extends RouteManager
{
}
