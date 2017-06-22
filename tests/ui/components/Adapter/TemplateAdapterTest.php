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

namespace Antares\Widgets\Adapter\Tests;

use Antares\UI\UIComponents\Adapter\TemplateAdapter as Stub;
use Antares\UI\UIComponents\UiComponentsServiceProvider;
//use Antares\Widgets\Memory\WidgetHandler;
use Antares\Testing\TestCase;
use Antares\Memory\Provider;
use Mockery as m;
use Exception;

class TemplateAdapterTest extends TestCase
{

    /**
     * @see parent::setUp
     */
    public function setUp()
    {
        parent::setUp();
        $serviceProvider = new UiComponentsServiceProvider($this->app);
        $serviceProvider->register();
        $serviceProvider->bootExtensionComponents();

        $provider = m::mock(Provider::class);
        $provider->shouldReceive('get')
                ->with('foo')
                ->andReturn(['name' => 'foo', 'path' => __DIR__ . '/../Fixtures/Widgets/templates'])
                ->shouldReceive('get')
                ->with(NULL)
                ->andReturn(['name' => 'foo', 'path' => __DIR__ . '/../Fixtures/Widgets/templates']);
        $memory   = m::mock(WidgetHandler::class);
        $memory->shouldReceive('make')
                ->with('widgets-templates')
                ->andReturn($provider);

        $this->app['antares.memory'] = $memory;
    }

    /**
     * test \Antares\Widgets\Adapter\TemplateAdapter::__construct
     * 
     * @test
     */
    public function testConstruct()
    {
        $stub = new Stub('foo');
        $this->assertInstanceOf('Antares\UI\UIComponents\Contracts\TemplateAdapter', $stub);
    }

    /**
     * test \Antares\Widgets\Adapter\TemplateAdapter::decorate
     * 
     * @test
     */
    public function testDecorate()
    {
        $stub = new Stub('foo');
        try {
            $stub->decorate('default');
        } catch (Exception $ex) {
            $this->assertInstanceOf('InvalidArgumentException', $ex);
        }
    }

}
