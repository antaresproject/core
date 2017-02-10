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
 namespace Antares\Resources\Routing\TestCase;

use Mockery as m;
use Illuminate\Routing\Controller;
use Antares\Resources\Routing\ControllerDispatcher;

class ControllerDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Resources\Routing\ControllerDispatcher::call() method
     * when method doesn't exist.
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testRunMethodThrowsException()
    {
        $container = m::mock('\Illuminate\Container\Container');
        $router    = m::mock('\Illuminate\Routing\Router');
        $route     = m::mock('\Illuminate\Routing\Route');
        $request   = m::mock('\Illuminate\Http\Request');
        $useFoo    = new FooController();

        $container->shouldReceive('make')->once()->with('FooController')->andReturn($useFoo)
            ->shouldReceive('bound')->once()->with('middleware.disable')->andReturn(false);

        $stub = new ControllerDispatcher($router, $container);

        $stub->dispatch($route, $request, 'FooController', 'getMissingMethod');
    }
}

class FooController extends Controller
{
    public function getIndex()
    {
        return 'FooController@getIndex';
    }
}
