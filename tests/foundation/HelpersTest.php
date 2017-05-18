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

namespace Antares\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;

class HelpersTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    private $app;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Application(__DIR__);

        $this->app['translator']  = $trans                    = m::mock('\Illuminate\Translation\Translator')->makePartial();
        $this->app['antares.app'] = $antares                  = m::mock('\Antares\Contracts\Foundation\Foundation');

        Facade::clearResolvedInstances();
        Container::setInstance($this->app);

        $trans->shouldReceive('trans')->andReturn('translated');
    }

    /**
     * Test antares() method.
     *
     * @test
     */
    public function testAntaresMethod()
    {
        $this->app['antares.platform.memory'] = m::mock('\Antares\Contracts\Memory\Provider');

        $this->assertInstanceOf('\Antares\Contracts\Foundation\Foundation', antares());
        $this->assertInstanceOf('\Antares\Contracts\Memory\Provider', antares('memory'));
    }

    /**
     * Test memorize() method.
     *
     * @test
     */
    public function testMemorizeMethod()
    {
        $this->app['antares.platform.memory'] = $memory                               = m::mock('\Antares\Contracts\Memory\Provider');

        $memory->shouldReceive('get')->once()->with('site.name', null)->andReturn('Antares');

        $this->assertEquals('Antares', memorize('site.name'));
    }

    /**
     * Test handles() method.
     *
     * @test
     */
    public function testHandlesMethod()
    {
        $antares = $this->app['antares.app'];

        $antares->shouldReceive('handles')->once()->with('app::foo', [])->andReturn('foo');

        $this->assertEquals('foo', handles('app::foo'));
    }

    /**
     * Test resources() method.
     *
     * @test
     */
    public function testResourcesMethod()
    {
        $antares = $this->app['antares.app'];

        $antares->shouldReceive('handles')->once()
                ->with('antares::resources/foo', [])->andReturn('foo');

        $this->assertEquals('foo', resources('foo'));
    }

    /**
     * Test get_meta() method.
     *
     * @test
     */
    public function testGetMetaMethod()
    {
        $this->app['antares.meta'] = $meta                      = m::mock('\Antares\Foundation\Meta');

        $meta->shouldReceive('get')->once()->with('title', 'foo')->andReturn('foobar');

        $this->assertEquals('foobar', get_meta('title', 'foo'));
    }

    /**
     * Test set_meta() method.
     *
     * @test
     */
    public function testSetMetaMethod()
    {
        $this->app['antares.meta'] = $meta                      = m::mock('\Antares\Foundation\Meta');
        $meta->shouldReceive('set')->once()->with('title', 'foo')->andReturnNull();
        $this->assertNull(set_meta('title', 'foo'));
    }

}
