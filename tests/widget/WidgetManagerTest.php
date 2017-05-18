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

namespace Antares\Widget\TestCase;

use Antares\Testbench\ApplicationTestCase;
use Antares\Widget\WidgetManager;
use Antares\Support\Collection;
use Antares\Support\Fluent;
use Mockery as m;

class WidgetManagerTest extends ApplicationTestCase
{

    /**
     * Test construct a new Antares\Widget\WidgetManager.
     *
     * @test
     */
    public function testConstructMethod()
    {

        $stub = new WidgetManager($this->app);

        $this->assertInstanceOf('\Antares\Widget\WidgetManager', $stub);
        $this->assertInstanceOf('\Antares\Support\Manager', $stub);
        $this->assertInstanceOf('\Illuminate\Support\Manager', $stub);
    }

    /**
     * Test Antares\Widget\WidgetManager::extend() method.
     *
     * @test
     */
    public function testExtendMethod()
    {
        $callback = function () {
            return 'foobar';
        };

        $stub = new WidgetManager($this->app);
        $stub->extend('foo', $callback);

        $refl           = new \ReflectionObject($stub);
        $customCreators = $refl->getProperty('customCreators');
        $customCreators->setAccessible(true);

        $this->assertEquals(['foo' => $callback], $customCreators->getValue($stub));

        $output = $stub->make('foo');

        $this->assertEquals('foobar', $output);
    }

    /**
     * Test Antares\Widget\WidgetManager::make() method for menu.
     *
     * @test
     */
    public function testMakeMethodForMenu()
    {
        $app = $this->app;

        $app['config'] = $config        = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('get')->once()
                ->with("antares/widget::menu.foo", m::any())->andReturn([])
                ->shouldReceive('get')->once()
                ->with("antares/widget::menu.foo.bar", m::any())->andReturn([]);

        $stub = with(new WidgetManager($app))->make('menu.foo');

        $this->assertInstanceOf('\Antares\Widget\Handlers\Menu', $stub);

        with(new WidgetManager($app))->make('menu.foo.bar');
    }

    /**
     * Test Antares\Widget\WidgetManager::make() method for pane.
     *
     * @test
     */
    public function testMakeMethodForPane()
    {
        $app           = $this->app;
        $app['config'] = $config        = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('get')->once()
                ->with("antares/widget::pane.foo", m::any())->andReturn([]);

        $stub = with(new WidgetManager($app))->make('pane.foo');

        $this->assertInstanceOf('\Antares\Widget\Handlers\Pane', $stub);
    }

    /**
     * Test Antares\Widget\WidgetManager::make() method for placeholder.
     *
     * @test
     */
    public function testMakeMethodForPlaceholder()
    {
        $app           = $this->app;
        $app['config'] = $config        = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('get')->once()
                ->with("antares/widget::placeholder.foo", m::any())->andReturn([]);

        $stub = with(new WidgetManager($app))->make('placeholder.foo');

        $this->assertInstanceOf('\Antares\Widget\Handlers\Placeholder', $stub);
    }

    /**
     * Test Antares\Widget\WidgetManager::make() using default driver method.
     *
     * @test
     */
    public function testMakeMethodForDefaultDriver()
    {
        $app           = $this->app;
        $app['config'] = $config        = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('get')->once()
                ->with("antares/widget::driver", 'placeholder.default')->andReturn('placeholder.default')
                ->shouldReceive('get')->once()
                ->with("antares/widget::placeholder.default", m::any())->andReturn([]);

        $stub = with(new WidgetManager($app))->driver();

        $this->assertInstanceOf('\Antares\Widget\Handlers\Placeholder', $stub);
    }

    /**
     * Test Antares\Widget\WidgetManager::setDefaultDriver() method.
     *
     * @rest
     */
    public function testSetDefaultDriverMethod()
    {
        $app           = $this->app;
        $app['config'] = $config        = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('set')->once()
                ->with('antares/widget::driver', 'foo')->andReturnNull();

        $stub = new WidgetManager($app);
        $stub->setDefaultDriver('foo');
    }

    /**
     * Test Antares\Widget\WidgetManager::make() method throws expection
     * for unknown widget type.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMakeMethodThrowsException()
    {
        with(new WidgetManager($this->app))->make('foobar');
    }

    /**
     * Test Antares\Widget\WidgetManager::of() method.
     *
     * @rest
     */
    public function testOfMethod()
    {
        $app           = $this->app;
        $app['config'] = $config        = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('get')->once()
                ->with("antares/widget::placeholder.foo", m::any())->andReturn([])
                ->shouldReceive('get')->once()
                ->with("antares/widget::placeholder.default", m::any())->andReturn([])
                ->shouldReceive('get')->once()
                ->with("antares/widget::driver", "placeholder.default")->andReturn("placeholder.default");

        $expected = new Collection([
            'foobar' => new Fluent([
                'id'      => 'foobar',
                'value'   => 'Hello world',
                'childs'  => [],
                'active'  => false,
                'content' => '']),
        ]);

        $stub1 = with(new WidgetManager($app))->of('placeholder.foo', function ($p) {
            $p->add('foobar')->value('Hello world');
        });

        $this->assertInstanceOf('\Antares\Widget\Handlers\Placeholder', $stub1);

        $this->assertEquals($expected, $stub1->items());

        $stub2 = with(new WidgetManager($app))->of(function ($p) {
            $p->add('foobar')->value('Hello world');
        });

        $this->assertInstanceOf('\Antares\Widget\Handlers\Placeholder', $stub2);
        $this->assertEquals($expected, $stub2->items());
    }

}
