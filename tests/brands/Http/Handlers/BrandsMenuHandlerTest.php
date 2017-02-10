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


namespace Antares\Brands\TestCase;

use Mockery as m;
use Antares\Brands\Http\Handlers\BrandsMenuHandler;
use Antares\Foundation\Support\MenuHandler;
use Antares\Testbench\TestCase;

class BrandsMenuHandlerTest extends TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testItIsInitializable()
    {

        $app  = m::mock('Illuminate\Container\Container');
        $menu = m::mock(Menu::class);
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $stub = new BrandsMenuHandler($app);
        $this->assertInstanceOf(BrandsMenuHandler::class, $stub);
        $this->assertInstanceOf(MenuHandler::class, $stub);
    }

    public function testItShouldBeChildOfExtensionGivenExtensionIsAvailable()
    {
        $app  = m::mock('Illuminate\Container\Container');
        $menu = m::mock(Menu::class);
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $menu->shouldReceive('has')->once()->with('extensions')->andReturn(true);
        $stub = new BrandsMenuHandler($app);
        $this->assertEquals('^:settings', $stub->getPositionAttribute());
    }

    public function testItShouldNextToHomeGivenExtensionIsntAvailable()
    {
        $app  = m::mock('Illuminate\Container\Container');
        $menu = m::mock(Menu::class);
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $menu->shouldReceive('has')->once()->with('extensions')->andReturn(false);
        $stub = new BrandsMenuHandler($app);
        $this->assertEquals('>:home', $stub->getPositionAttribute());
    }

    /**
     * testing authorize method
     */
    public function testAuthorize()
    {

        $this->app['antares.platform.memory'] = m::mock('Antares\Memory\Provider');
        $app                                  = m::mock('Illuminate\Container\Container');
        $menu                                 = m::mock(Menu::class);
        $acl                                  = m::mock('Antares\Authorization\Factory')
                ->shouldReceive('make')
                ->with("antares/widgets")
                ->andReturnSelf()
                ->shouldReceive('make')
                ->with("antares/brands")
                ->andReturnSelf()
                ->shouldReceive("can")
                ->with(m::type("String"))
                ->andReturn(true)
                ->shouldReceive('attach')
                ->with($this->app['antares.platform.memory'])
                ->andReturnSelf()
                ->getMock();
        $this->app['antares.acl']             = $acl;
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $stub                                 = new BrandsMenuHandler($app);
        $guardMock                            = m::mock('\Antares\Contracts\Auth\Guard');
        $guardMock->shouldReceive('guest')->andReturn(false);
        $this->assertTrue($stub->authorize($guardMock));
    }

    /**
     * test handle method
     */
    public function testHandle()
    {
        $this->app['antares.platform.memory']      = m::mock('Antares\Memory\Provider');
        $this->app['antares.platform.menu']        = $menu                                      = m::mock('\Antares\Widget\Handlers\Menu');
        $stub                                      = new BrandsMenuHandler($this->app);
        $acl                                       = m::mock('Antares\Authorization\Factory')
                ->shouldReceive('make')
                ->with("antares/widgets")
                ->andReturnSelf()
                ->shouldReceive('make')
                ->with("antares/brands")
                ->andReturnSelf()
                ->shouldReceive("can")
                ->with(m::type("String"))
                ->andReturn(true)
                ->shouldReceive('attach')
                ->with($this->app['antares.platform.memory'])
                ->andReturnSelf()
                ->getMock();
        $this->app['antares.acl']                  = $acl;
        $guardMock                                 = m::mock('\Antares\Contracts\Auth\Guard');
        $guardMock->shouldReceive('guest')->andReturn(false);
        $this->app['Antares\Contracts\Auth\Guard'] = $guard                                     = m::mock('Antares\Contracts\Auth\Guard');
        $menu->shouldReceive('has')->with(m::type('String'))->andReturn(false)
                ->shouldReceive('add')->with(m::type('String'), m::type('String'))->andReturnSelf()
                ->shouldReceive('title')->with(m::type('String'))->andReturnSelf()
                ->shouldReceive('link')->with(m::type('String'))->andReturn($fluent                                    = m::mock('Illuminate\Support\Fluent'));

        $fluent->shouldReceive('icon')->with(m::type('String'))->andReturnSelf();

        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $foundation->shouldReceive('handles')->with(m::type('String'))->andReturn('#url');

        $this->app['antares.app'] = $foundation;

        $stub->authorize($guardMock);
        $this->assertNull($stub->handle());
    }

}
