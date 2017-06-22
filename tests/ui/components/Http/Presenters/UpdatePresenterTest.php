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
 * @package    Widgets
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Widgets\Http\Presenters\Tests;

use Antares\UI\UIComponents\Http\Presenters\UpdatePresenter as Stub;
use Antares\Widgets\Tests\Fixtures\Widgets\WidgetTest;
use Antares\UI\UIComponents\Http\Presenters\UpdatePresenter;
use Antares\UI\UIComponents\Contracts\AfterValidate;
use Antares\UI\UIComponents\UiComponentsServiceProvider;
use Antares\Contracts\Html\Form\Factory;
use Antares\UI\UIComponents\Repository\Repository;
use Illuminate\Support\Fluent;
use Antares\Testing\TestCase;
use Antares\Memory\Provider;
use Mockery as m;

class UpdatePresenterTest extends TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $serviceProvider = new UiComponentsServiceProvider($this->app);
        $serviceProvider->register();
        $serviceProvider->bootExtensionComponents();
    }

    /**
     * test \Antares\Widgets\Http\Presenters\UpdatePresenter::__construct()
     * 
     * @test
     */
    public function testConstruct()
    {
        $formFactory          = m::mock(Factory::class);
        $afterValidateAdapter = m::mock(AfterValidate::class);
        $this->assertInstanceOf(UpdatePresenter::class, new Stub($formFactory, $afterValidateAdapter));
    }

    /**
     * test \Antares\Widgets\Http\Presenters\UpdatePresenter::form()
     * 
     * @expectedException \Antares\UI\UIComponents\Exception\TemplateIndexNotFoundException
     */
    public function testForm()
    {
        spl_autoload_register(function ($class) {
            if ($class == WidgetTest::class) {
                include __DIR__ . '/../../Fixtures/Widgets/WidgetTest.php';
            }
        });

        $provider = m::mock(Provider::class);
        $provider->shouldReceive('get')->with('foo.name')->andReturn(WidgetTest::class);
        $provider->shouldReceive('get')->with('default')->andReturn([
            'path' => __DIR__ . '/../../Fixtures/Widgets/templates'
        ]);
        $provider->shouldReceive('raw')->andReturn([]);
        $memory   = m::mock(\Antares\Widgets\Memory\WidgetHandler::class);
        $memory->shouldReceive('get')->with('foo.name')->andReturn(['testowanie']);
        $memory->shouldReceive('make')->with('ui-components')->andReturn($provider)
                ->shouldReceive('make')->with('ui-components-templates')->andReturn($provider);


        $this->app['antares.memory'] = $memory;
        $factory                     = m::mock(Factory::class);
        $factory2                    = m::mock(Factory::class);
        $fluent                      = m::mock(Fluent::class);
        $factory2->grid              = $fluent;
        $factory->shouldReceive('of')->with(m::type('String'), m::type('Closure'))->andReturn($factory2);

        $afterValidateAdapter = m::mock(AfterValidate::class);

        $instance = new Stub($factory, $afterValidateAdapter);

        $model = m::mock(Repository::class);
        $model->shouldReceive('getAttribute')->andReturn(1);
        $this->assertTrue(is_object($instance->form('foo', $model, '/')));
    }

}
