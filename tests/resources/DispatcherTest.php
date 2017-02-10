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
 namespace Antares\Resources\TestCase;

use Mockery as m;
use Illuminate\Routing\Controller;
use Antares\Resources\Router;
use Antares\Resources\Dispatcher;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    private $app = null;

    /**
     * Router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    private $router = null;

    /**
     * Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app     = m::mock('\Illuminate\Container\Container');
        $this->router  = m::mock('\Illuminate\Routing\Router');
        $this->request = m::mock('\Illuminate\Http\Request');
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        unset($this->router);
        unset($this->request);
        m::close();
    }

    /**
     * Test Antares\Resources\Dispatcher::call() method using GET verb.
     *
     * @test
     */
    public function testCallMethodUsingGetVerb()
    {
        $app        = $this->app;
        $router     = $this->router;
        $request    = $this->request;
        $useApp     = new AppController();
        $useFoo     = new FooController();
        $useFoobar  = new FoobarController();

        $app->shouldReceive('make')->with('AppController')->once()->andReturn($useApp)
            ->shouldReceive('make')->with('FooController')->once()->andReturn($useFoo)
            ->shouldReceive('make')->times(3)->with('FoobarController')->andReturn($useFoobar)
            ->shouldReceive('bound')->with('middleware.disable')->andReturn(false);
        $request->shouldReceive('getMethod')->times(6)->andReturn('GET');
        $router->shouldReceive('prepareResponse')->times(5)->with($request, m::type('String'))
                ->andReturnUsing(function($request, $response) {
                    return $response;
                });

        $driver = new Router('app', [
            'name'   => 'app',
            'uses'   => 'AppController',
            'routes' => [
                'foo'     => 'restful:FooController',
                'foo.bar' => 'resource:FoobarController',
            ],
        ]);

        $stub = new Dispatcher($app, $router, $request);

        $this->assertEquals('AppController@getIndex', $stub->call($driver, null, ['index']));
        $this->assertEquals('FooController@getEdit', $stub->call($driver, 'foo', ['edit']));
        $this->assertEquals('FoobarController@edit', $stub->call($driver, 'foo', [1, 'bar', 2, 'edit']));
        $this->assertEquals('FoobarController@index', $stub->call($driver, 'foo', [1, 'bar']));
        $this->assertEquals('FoobarController@show', $stub->call($driver, 'foo', [1, 'bar', 2]));
        $this->assertFalse($stub->call($driver, 'not-available'));
    }

    /**
     * Test Antares\Resources\Dispatcher::call() method using POST verb.
     *
     * @test
     */
    public function testCallMethodUsingPostVerb()
    {
        $app       = $this->app;
        $router    = $this->router;
        $request   = $this->request;
        $useFoobar = new FoobarController();

        $app->shouldReceive('make')->once()->with('FoobarController')->andReturn($useFoobar)
            ->shouldReceive('bound')->with('middleware.disable')->andReturn(false);
        $request->shouldReceive('getMethod')->once()->andReturn('POST');
        $router->shouldReceive('prepareResponse')->once()->with($request, m::type('String'))
                ->andReturnUsing(function($request, $response) {
                    return $response;
                });

        $driver = new Router('app', [
            'name'   => 'app',
            'uses'   => 'AppController',
            'routes' => [
                'foo'     => 'restful:FooController',
                'foo.bar' => 'resource:FoobarController',
            ],
        ]);

        $stub = new Dispatcher($app, $router, $request);

        $this->assertEquals('FoobarController@store', $stub->call($driver, 'foo', [1, 'bar', 2]));
    }

    /**
     * Test Antares\Resources\Dispatcher::call() method using PUT verb.
     *
     * @test
     */
    public function testCallMethodUsingPutVerb()
    {
        $app       = $this->app;
        $router    = $this->router;
        $request   = $this->request;
        $useFoobar = new FoobarController();

        $app->shouldReceive('make')->once()->with('FoobarController')->andReturn($useFoobar)
            ->shouldReceive('bound')->with('middleware.disable')->andReturn(false);
        $request->shouldReceive('getMethod')->once()->andReturn('PUT');
        $router->shouldReceive('prepareResponse')->once()->with($request, m::type('String'))
                ->andReturnUsing(function($request, $response) {
                    return $response;
                });

        $driver = new Router('app', [
            'name'   => 'app',
            'uses'   => 'AppController',
            'routes' => [
                'foo'     => 'restful:FooController',
                'foo.bar' => 'resource:FoobarController',
            ],
        ]);

        $stub = new Dispatcher($app, $router, $request);

        $this->assertEquals('FoobarController@update', $stub->call($driver, 'foo', [1, 'bar', 2]));
    }

    /**
     * Test Antares\Resources\Dispatcher::call() method using GET verb.
     *
     * @test
     */
    public function testCallMethodUsingDeleteVerb()
    {
        $app       = $this->app;
        $router    = $this->router;
        $request   = $this->request;
        $useFoobar = new FoobarController();

        $app->shouldReceive('make')->once()->with('FoobarController')->andReturn($useFoobar)
            ->shouldReceive('bound')->with('middleware.disable')->andReturn(false);
        $request->shouldReceive('getMethod')->once()->andReturn('DELETE');
        $router->shouldReceive('prepareResponse')->once()->with($request, m::type('String'))
                ->andReturnUsing(function($request, $response) {
                    return $response;
                });

        $driver = new Router('app', [
            'name'   => 'app',
            'uses'   => 'AppController',
            'routes' => [
                'foo'     => 'restful:FooController',
                'foo.bar' => 'resource:FoobarController',
            ],
        ]);

        $stub = new Dispatcher($app, $router, $request);

        $this->assertEquals('FoobarController@destroy', $stub->call($driver, 'foo', [1, 'bar', 2]));
    }

    /**
     * Test Antares\Resources\Dispatcher::call() method throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCallMethodThrowsException()
    {
        $app     = $this->app;
        $request = $this->request;
        $router  = $this->router;
        $stub    = new Dispatcher($app, $router, $request);

        $request->shouldReceive('getMethod')->once()->andReturn('GET');

        $driver = new Router('app', [
            'name'   => 'app',
            'uses'   => 'request:AppController',
            'childs' => [],
        ]);

        $stub->call($driver, null, ['edit']);
    }
}

class AppController extends Controller
{
    public function getIndex()
    {
        return 'AppController@getIndex';
    }
}

class FooController extends Controller
{
    public function getEdit()
    {
        return 'FooController@getEdit';
    }
}

class FoobarController extends Controller
{
    public function index()
    {
        return 'FoobarController@index';
    }

    public function show()
    {
        return 'FoobarController@show';
    }

    public function store()
    {
        return 'FoobarController@store';
    }

    public function edit()
    {
        return 'FoobarController@edit';
    }

    public function update()
    {
        return 'FoobarController@update';
    }

    public function destroy()
    {
        return 'FoobarController@destroy';
    }
}
