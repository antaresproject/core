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
use Antares\Brands\Http\Handlers\BrandsSelectorHandler;
use Antares\Testbench\TestCase;

class BrandsSelectorHandlerTest extends TestCase
{

    /**
     * test GetTitleAttribute
     */
    public function testGetTitleAttribute()
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
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu)
                ->shouldReceive('make')->once()->with('antares.menu.brands')->andReturn($menu)
                ->shouldReceive('make')->once()->with('translator')->andReturn($translator                           = m::mock('Illuminate\Translation\Translator'));

        $translator->shouldReceive('trans')->with('foo')->andReturn('foo');
        $stub = new BrandsSelectorHandler($app);
        $this->assertSame('foo', $stub->getTitleAttribute('foo'));
    }

    /**
     * testing authorize methoda
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
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu)
                ->shouldReceive('make')->once()->with('antares.menu.brands')->andReturn($menu)
                ->shouldReceive('make')->once()->with('translator')->andReturn($translator                           = m::mock('Illuminate\Translation\Translator'));

        $stub      = new BrandsSelectorHandler($app);
        $guardMock = m::mock('Antares\Contracts\Authorization\Authorization');


        $this->assertTrue($stub->authorize($guardMock));
    }

}
