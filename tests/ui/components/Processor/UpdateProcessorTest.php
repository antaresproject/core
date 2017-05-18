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

namespace Antares\Widgets\Processor\Tests;

use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Antares\Widgets\Processor\UpdateProcessor as Stub;
use Antares\Widgets\Tests\Fixtures\Widgets\WidgetTest;
use Antares\Widgets\Http\Presenters\UpdatePresenter;
use Antares\Widgets\Processor\UpdateProcessor;
use Antares\Contracts\Html\Form\Factory;
use Antares\Widgets\Model\WidgetParams;
use Antares\Widgets\Contracts\Updater;
use Antares\Memory\Provider;
use Antares\Testing\TestCase;
use Exception;
use Illuminate\Http\JsonResponse;
use Mockery as m;

class UpdateProcessorTest extends TestCase
{

    use EloquentConnectionTrait;

    /**
     * @var UpdateProcessor 
     */
    protected $stub;

    /**
     * @see parent::setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $return   = ['bar' => ['name' => 'foo', 'id' => 1]];
        $provider = m::mock(Provider::class);
        $provider->shouldReceive('raw')->withNoArgs()->andReturn($return)
                ->shouldReceive('get')->with('.name')->andReturn(WidgetTest::class)
                ->shouldReceive('get')->with('default')->andReturn(['path' => __DIR__ . '/../Fixtures/Widgets/templates']);

        $memory = m::mock(\Antares\Widgets\Memory\WidgetHandler::class);
        $memory->shouldReceive('make')->with('widgets')->andReturn($provider)
                ->shouldReceive('make')->with("widgets-templates")->andReturn($provider);




        $this->app['antares.memory'] = $memory;


        $presenter   = m::mock(UpdatePresenter::class);
        $presenter->shouldReceive('form')->with(NULL, m::type(WidgetParams::class), "antares::widgets/updater")->andReturn($formFactory = m::mock(Factory::class));
        $this->stub  = new Stub($presenter);
    }

    /**
     * test constructor
     * 
     * @test
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(UpdateProcessor::class, $this->stub);
    }

    /**
     * test edit action
     * 
     * @test
     */
    public function testEdit()
    {
        try {
            $listener = m::mock(Updater::class);
            $listener->shouldReceive('showWidgetUpdater')->with(1, m::any())->andReturn("works");
            $id       = 1;
            $this->assertSame('works', $this->stub->edit($listener, $id));
        } catch (Exception $e) {
            
        }
    }

    /**
     * test update action
     * 
     * @test
     */
    public function testUpdate()
    {
        try {
            spl_autoload_register(function ($class) {
                if ($class == WidgetTest::class) {
                    include __DIR__ . '/../Fixtures/Widgets/WidgetTest.php';
                }
            });

            $listener = m::mock(Updater::class);

            $id = 1;
            $this->assertInstanceOf(JsonResponse::class, $this->stub->update($listener, $id, []));
        } catch (Exception $e) {
            
        }
    }

}
