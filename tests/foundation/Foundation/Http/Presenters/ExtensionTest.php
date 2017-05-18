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

namespace Antares\Foundation\Http\Presenters\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Antares\Foundation\Http\Presenters\Extension;

class ExtensionTest extends \PHPUnit_Framework_TestCase
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
        $this->app['translator']  = m::mock('\Illuminate\Translation\Translator')->makePartial();

        $this->app['antares.app']->shouldReceive('handles');
        $this->app['translator']->shouldReceive('trans');

        Facade::clearResolvedInstances();
        Container::setInstance($this->app);
    }

    /**
     * Test Antares\Foundation\Http\Presenters\Extension::form()
     * method.
     *
     * @test
     */
    public function testFormMethod()
    {
        $this->markTestIncomplete('Component configuration is not completed yet.');

        $model       = new Fluent();
        $app         = $this->app;
        $app['html'] = m::mock('\Antares\Html\HtmlBuilder')->makePartial();

        $form      = m::mock('\Antares\Contracts\Html\Form\Factory');
        $extension = m::mock('\Antares\Contracts\Extension\Factory');

        $grid     = m::mock('\Antares\Contracts\Html\Form\Grid');
        $fieldset = m::mock('\Antares\Contracts\Html\Form\Fieldset');
        $control  = m::mock('\Antares\Contracts\Html\Form\Control');

        $breadcrumb = m::mock('\Antares\Foundation\Http\Breadcrumb\Breadcrumb');

        $breadcrumb->shouldReceive('onComponentConfigure')->andReturnSelf();
        $breadcrumb->shouldReceive('onComponentsList')->andReturnSelf();

        $extensions = m::mock(\Antares\Foundation\Http\Datatables\Extensions::class);
        $extensions->shouldReceive('render')->andReturn('foo');

        $stub = new Extension($form, $breadcrumb, $extensions);

        $control->shouldReceive('label')->twice()->andReturnSelf()
                ->shouldReceive('value')->once()->andReturnSelf()
                ->shouldReceive('field')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) {
                    $c();
                });
        $fieldset->shouldReceive('control')->twice()->with('input:text', m::any())->andReturn($control);
        $grid->shouldReceive('setup')->once()->with($stub, 'antares::extensions/foo/bar/configure', $model)->andReturnNull()
                ->shouldReceive('fieldset')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($fieldset) {
                    $c($fieldset);
                });
        $extension->shouldReceive('option')->once()->with('foo/bar', 'handles')->andReturn('foo');
        $form->shouldReceive('of')->once()
                ->with('antares.extension: foo/bar', m::type('Closure'))
                ->andReturnUsing(function ($t, $c) use ($grid) {
                    $c($grid);

                    return 'foo';
                });

        $app['html']->shouldReceive('link')->once()
                ->with(handles("antares/foundation::extensions/foo/bar/update"), m::any(), m::any())
                ->andReturn('foo');

        $this->assertEquals('foo', $stub->configure($model, 'foo/bar'));
    }

    public function testTable()
    {
        $this->markTestIncomplete('Component configuration table is not completed yet.');

        $form       = m::mock('\Antares\Contracts\Html\Form\Factory');
        $breadcrumb = m::mock('\Antares\Foundation\Http\Breadcrumb\Breadcrumb');
        $breadcrumb->shouldReceive('onComponentConfigure')->andReturnSelf()
                ->shouldReceive('onComponentsList')->andReturnSelf();

        $extensions = m::mock(\Antares\Foundation\Http\Datatables\Extensions::class);
        $extensions->shouldReceive('render')->andReturn('foo');

        $stub = new Extension($form, $breadcrumb, $extensions);
        $this->assertEquals('foo', $stub->table());
    }

}
