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

use Antares\UI\UIComponents\Processor\DestroyProcessor as Stub;
use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Antares\UI\UIComponents\UiComponentsServiceProvider;
use Antares\UI\UIComponents\Contracts\Destroyer;
use Illuminate\Container\Container;
use Antares\Testing\TestCase;
use Mockery as m;

class DestroyProcessorTest extends TestCase
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
    }

    /**
     * test \Antares\Widgets\Processor\DestroyProcessor::disable()
     * 
     * @test
     */
    public function testDisable()
    {
        $container = $this->app->make(Container::class);
        $stub      = new Stub($container);
        $listener  = m::mock(Destroyer::class);
        $listener->shouldReceive('whenDestroyError')->with(m::any())->andReturn(false);

        $this->assertFalse($stub->disable($listener, 999));
    }

}
