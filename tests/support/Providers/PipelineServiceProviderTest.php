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

namespace Antares\Support\Providers\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Support\Providers\PipelineServiceProvider;

class PipelineServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Support\Providers\PipelineServiceProvider method signature.
     *
     * @test
     */
    public function testInstanceSignature()
    {
        $stub = new StubPipelineProvider(null);

        $this->assertContains('Antares\Support\Providers\Traits\FilterProviderTrait', class_uses_recursive(get_class($stub)));
        $this->assertContains('Antares\Support\Providers\Traits\MiddlewareProviderTrait', class_uses_recursive(get_class($stub)));
    }

    /**
     * Test Antares\Support\Providers\PipelineServiceProvider::register()
     * method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $stub = new StubPipelineProvider(null);

        $this->assertContains('Antares\Support\Providers\Traits\MiddlewareProviderTrait', class_uses_recursive(get_class($stub)));

        $this->assertNull($stub->register());
    }

    /**
     * Test Antares\Support\Providers\PipelineServiceProvider::boot()
     * method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $app = new Container();

        $router = m::mock('\Illuminate\Routing\Router');
        $kernel = m::mock('\Illuminate\Contracts\Http\Kernel');

        $router->shouldReceive('before')->once()->with('BeforeFilter')->andReturnNull()
                ->shouldReceive('after')->once()->with('AfterFilter')->andReturnNull()
                ->shouldReceive('filter')->once()->with('foo', 'FooFilter')->andReturnNull()
                ->shouldReceive('aliasMiddleware')->once()->with('foobar', 'FoobarMiddleware')->andReturnNull()
                ->shouldReceive('aliasMiddleware')->once()->with('', "Antares\Form\Middleware\FormMiddleware")->andReturnNull();

        $kernel->shouldReceive('pushMiddleware')->once()->with('FooMiddleware')->andReturnNull();

        $stub = new StubPipelineProvider($app);
        $this->assertNull($stub->boot($router, $kernel));
    }

}

class StubPipelineProvider extends PipelineServiceProvider
{

    protected $before           = ['BeforeFilter'];
    protected $after            = ['AfterFilter'];
    protected $filters          = ['foo' => 'FooFilter'];
    protected $middleware       = ['FooMiddleware'];
    protected $routeMiddleware  = ['foobar' => 'FoobarMiddleware'];
    protected $middlewareGroups = [];

}
