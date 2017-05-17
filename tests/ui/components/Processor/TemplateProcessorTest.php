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

use Antares\Widgets\Processor\TemplateProcessor as Stub;
use Antares\Widgets\WidgetsServiceProvider;
use Antares\Widgets\Memory\WidgetHandler;
use Antares\Testing\TestCase;
use Antares\Memory\Provider;
use Mockery as m;

class TemplateProcessorTest extends TestCase
{

    /**
     * @see inherit
     */
    public function setUp()
    {
        parent::setUp();
        $serviceProvider = new WidgetsServiceProvider($this->app);
        $serviceProvider->register();
        $serviceProvider->bootExtensionComponents();
    }

    /**
     * test \Antares\Widgets\Processor\TemplateProcessor::index()
     * 
     * @test
     */
    public function testIndex()
    {
        $return   = ['bar' => ['name' => 'foo', 'id' => 1]];
        $provider = m::mock(Provider::class);
        $provider->shouldReceive('all')
                ->withNoArgs()
                ->andReturn($return);

        $memory = m::mock(WidgetHandler::class);
        $memory->shouldReceive('make')
                ->with('widgets-templates')
                ->andReturn($provider);



        $this->app['antares.memory'] = $memory;

        $stub  = new Stub();
        $index = $stub->index();
        $this->assertTrue(!empty($index));
        $this->assertTrue(isset($index['data']));
        $data  = $index['data'];
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('default', $data);
    }

}
