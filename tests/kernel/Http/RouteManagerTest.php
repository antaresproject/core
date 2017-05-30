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

namespace Antares\Http\TestCase;

use Antares\Testing\ApplicationTestCase;
use Illuminate\Support\Facades\Facade;
use Antares\Http\RouteManager;
use Mockery as m;

class RouteManagerTest extends ApplicationTestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $_SERVER['RouteManagerTest@callback'] = null;
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this->app);
    }

    /**
     * Installed setup.
     */
    private function getApplicationMocks()
    {
        $app     = $this->app;
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


        $stub = new StubRouteManager($this->app);

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
        $group = [
            'before' => 'auth',
            'prefix' => 'admin',
            'domain' => null,
        ];

        $callback = function () {
            
        };

        $stub = new StubRouteManager($this->app);

        $this->assertEquals($group, $stub->group('admin', 'admin', ['before' => 'auth'], $callback));
    }

    /**
     * Test Antares\Http\RouteManager::group() method
     * with closure and not array.
     *
     * @expectedException ErrorException
     */
    public function testGroupMethodWithClosureAndNotArray()
    {
        $group = [
            'prefix' => 'admin',
            'domain' => null,
        ];

        $callback = function () {
            
        };
        $stub = new StubRouteManager($this->app);
        $this->assertEquals($group, $stub->group('admin', 'admin', $callback));
    }

    /**
     * Test Antares\Http\RouteManager::handles() method.
     *
     * @test
     */
    public function testHandlesMethod()
    {
        $stub = new StubRouteManager($this->app);

        $this->assertEquals('http://localhost', $stub->handles('app::/'));
        $this->assertEquals('http://localhost/info?foo=bar', $stub->handles('info?foo=bar'));
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
        $this->app['session'] = $session              = m::mock(\Illuminate\Session\SessionManager::class);
        $session->shouldReceive('token')->andReturn('StAGiQ');
        $stub                 = new StubRouteManager($this->app);

        $options = ['csrf' => true];
        $this->assertEquals('http://localhost/?_token=StAGiQ', $stub->handles('app::/', $options));
        $this->assertEquals('http://localhost/info?foo=bar&_token=StAGiQ', $stub->handles('info?foo=bar', $options));
    }

    /**
     * Test Antares\Http\RouteManager::is() method.
     *
     * @test
     */
    public function testIsMethod()
    {
        $stub = new StubRouteManager($this->app);

        $this->assertTrue($stub->is('app::/'));
        $this->assertFalse($stub->is('info?foo=bar'));
    }

}

class StubRouteManager extends RouteManager
{
    
}
