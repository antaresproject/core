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
 namespace Antares\Foundation\Http\Presenters\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Antares\Foundation\Http\Presenters\Resource;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();

        $this->app['antares.app'] = m::mock('\Antares\Contracts\Foundation\Foundation');
        $this->app['translator']    = m::mock('\Illuminate\Translation\Translator')->makePartial();

        $this->app['antares.app']->shouldReceive('handles');
        $this->app['translator']->shouldReceive('trans');

        Facade::clearResolvedInstances();
        Container::setInstance($this->app);
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);

        m::close();
    }

    /**
     * Test Antares\Foundation\Presenter\Resource::table()
     * method.
     *
     * @test
     */
    public function testTableMethod()
    {
        $app   = $this->app;
        $model = new Fluent();
        $value = (object) [
            'id'   => 'foo',
            'name' => 'Foobar',
        ];

        $app['html'] = m::mock('\Antares\Html\HtmlBuilder')->makePartial();

        $table  = m::mock('\Antares\Contracts\Html\Table\Factory');
        $grid   = m::mock('\Antares\Contracts\Html\Table\Grid');
        $column = m::mock('\Antares\Contracts\Html\Table\Column');

        $stub = new Resource($table);

        $column->shouldReceive('escape')->once()->with(false)->andReturnSelf()
            ->shouldReceive('value')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($value) {
                    $c($value);
                });
        $grid->shouldReceive('with')->once()->with($model, false)->andReturnNull()
            ->shouldReceive('layout')->once()->with('antares/foundation::components.table')->andReturnNull()
            ->shouldReceive('column')->once()->with('name')->andReturn($column);
        $table->shouldReceive('of')->once()
                ->with('antares.resources: list', m::type('Closure'))
                ->andReturnUsing(function ($t, $c) use ($grid) {
                    $c($grid);

                    return 'foo';
                });

        $app['html']->shouldReceive('create')->once()->with('strong', 'Foobar')->andReturn('foo')
            ->shouldReceive('raw')->once()->with('foo')->andReturn('Foobar')
            ->shouldReceive('link')->once()
                ->with(handles("antares/foundation::resources/foo"), e("Foobar"))->andReturn('foo');

        $this->assertEquals('foo', $stub->table($model));
    }
}
