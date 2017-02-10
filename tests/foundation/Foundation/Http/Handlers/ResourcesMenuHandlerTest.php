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
 namespace Antares\Foundation\Http\Handlers\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Illuminate\Container\Container;
use Antares\Foundation\Http\Handlers\ResourcesMenuHandler;

class ResourcesMenuHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Antares\Foundation\Http\Handlers\ResourcesMenuHandler::handle()
     * method with resources.
     *
     * @test
     */
    public function testCreatingMenuWithResources()
    {
        $app = new Container();
        $app['antares.resources'] = $resources = m::mock('\Antares\Resources\Factory');
        $app['antares.app'] = $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $app['antares.platform.menu'] = $menu = m::mock('\Antares\Widget\Handlers\Menu');
        $app['translator'] = $translator = m::mock('\Illuminate\Translator\Translator');

        $foo = new Fluent([
            'name'    => 'Foo',
            'visible' => true,
        ]);

        $bar = new Fluent([
            'name'    => 'Bar',
            'visible' => false,
        ]);

        $resources->shouldReceive('all')->once()->andReturn(compact('foo', 'bar'));

        $translator->shouldReceive('trans')->once()->with('antares/foundation::title.resources.list')->andReturn('resources');
        $foundation->shouldReceive('handles')->once()->with('antares::resources')->andReturn('admin/resources');
        $menu->shouldReceive('add')->once()->with('resources', '>:extensions')->andReturnSelf()
            ->shouldReceive('title')->once()->with('resources')->andReturnSelf()
            ->shouldReceive('link')->once()->with('admin/resources')->andReturnNull();

        $foundation->shouldReceive('handles')->once()->with('antares::resources/foo')->andReturn('foo-resource');
        $menu->shouldReceive('add')->once()->with('foo', '^:resources')->andReturnSelf()
            ->shouldReceive('title')->once()->with('Foo')->andReturnSelf()
            ->shouldReceive('link')->once()->with('foo-resource')->andReturnNull();

        $stub = new ResourcesMenuHandler($app);
        $this->assertNull($stub->handle());
    }

    /**
     * Test Antares\Foundation\Http\Handlers\ResourcesMenuHandler::handle()
     * method without resources.
     *
     * @test
     */
    public function testCreatingMenuWithoutResources()
    {
        $app = new Container();
        $app['antares.resources'] = $resources = m::mock('\Antares\Resources\Factory');
        $app['antares.app'] = $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $app['antares.platform.menu'] = $menu = m::mock('\Antares\Widget\Handlers\Menu');
        $app['translator'] = $translator = m::mock('\Illuminate\Translator\Translator');

        $foo = new Fluent([
            'name'    => 'Foo',
            'visible' => false,
        ]);

        $bar = new Fluent([
            'name'    => 'Bar',
            'visible' => false,
        ]);

        $resources->shouldReceive('all')->once()->andReturn(compact('foo', 'bar'));

        $stub = new ResourcesMenuHandler($app);
        $this->assertTrue($stub->authorize());
        $this->assertNull($stub->handle());
    }

    /**
     * Test Antares\Foundation\Http\Handlers\ResourcesMenuHandler::handle()
     * method without `antares.resources` bound to container.
     *
     * @test
     */
    public function testCreatingMenuWithoutBoundDependencies()
    {
        $app = new Container();
        $app['antares.app'] = $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $app['antares.platform.menu'] = $menu = m::mock('\Antares\Widget\Handlers\Menu');
        $app['translator'] = $translator = m::mock('\Illuminate\Translator\Translator');

        $stub = new ResourcesMenuHandler($app);
        $this->assertFalse($stub->authorize());
        $this->assertNull($stub->handle());
    }
}
