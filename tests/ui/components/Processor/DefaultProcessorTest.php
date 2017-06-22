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

namespace Antares\UI\UIComponents\Processor\Tests;

use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Antares\UI\UIComponents\Processor\DefaultProcessor as Stub;
use Antares\UI\UIComponents\Exception\WidgetNotFoundException;
use Antares\UI\UIComponents\Processor\DefaultProcessor;
use Antares\UI\UIComponents\UiComponentsServiceProvider;
use Antares\UI\UIComponents\Memory\WidgetHandler;
use Antares\UI\UIComponents\Contracts\GridStack;
use Antares\UI\UIComponents\Model\ComponentParams;
use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Antares\Testing\TestCase;
use Antares\Memory\Provider;
use Mockery as m;

class DefaultProcessorTest extends TestCase
{

    use EloquentConnectionTrait;

    /**
     * @see inherit
     */
    public function setUp()
    {
        parent::setUp();
        $serviceProvider = new UiComponentsServiceProvider($this->app);
        $serviceProvider->register();
        $serviceProvider->bootExtensionComponents();
        $return          = ['bar' => ['name' => 'foo', 'id' => 1]];
        $provider        = m::mock(Provider::class);

        $provider->shouldReceive('raw')->withNoArgs()->andReturn($return)
                ->shouldReceive('raw')->with("")->andReturn($return);

        $memory = m::mock(WidgetHandler::class);
        $memory->shouldReceive('make')->with('widgets')->andReturn($provider)
                ->shouldReceive('forgetCache')->with(Null)->andReturn(true)
                ->shouldReceive('extend')->with('collector', m::type('Object'))->andReturn(null)
                ->shouldReceive('extend')->with('forms-config', m::type('Object'))->andReturn(null);


        $provider->shouldReceive('getHandler')->withNoArgs()->andReturn($memory);

        $this->app['antares.memory'] = $memory;
    }

    /**
     * Test Antares\UI\UIComponents\Processor\DefaultProcessor::__construct() method.
     *
     * @test
     */
    public function testConstruct()
    {
        $adapter = m::mock(GridStack::class, $this->app->make(Container::class));
        $stub    = new Stub($adapter, $this->app);
        $this->assertSame(get_class($stub), DefaultProcessor::class);
    }

    /**
     * Test Antares\UI\UIComponents\Processor\DefaultProcessor::show() method.
     *
     * @test
     */
    public function testShow()
    {
        $adapter = m::mock(GridStack::class);
        $stub    = new Stub($adapter, $this->app->make(Container::class));
        $show    = $stub->show(1);
        $this->assertSame("Session store not set on request.", $show);
    }

    /**
     * Test positions method
     * 
     * @test
     */
    public function testPositions()
    {
        $adapter                           = m::mock(GridStack::class);
        $stub                              = new Stub($adapter, $this->app->make(Container::class));
        $data                              = [
            'widgets' => [
                'foo' => [
                    'attributes' => [
                        'foo' => 'bar'
                    ],
                    'widgetId'   => 1
                ]
            ]
        ];
        $this->app[ComponentParams::class] = m::mock(ComponentParams::class);
        $this->assertInstanceOf(JsonResponse::class, $stub->positions($data));
    }

    /**
     * view method test
     * 
     * @test
     */
    public function testView()
    {
        $adapter = m::mock(GridStack::class);
        $stub    = new Stub($adapter, $this->app->make(Container::class));

        try {
            $stub->view(1);
        } catch (Exception $ex) {
            $this->assertInstanceOf(WidgetNotFoundException::class, $ex);
        }
    }

}
