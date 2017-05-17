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

namespace Antares\Widgets\Tests;

use Mockery as m;
use Antares\Widgets\Factory as Stub;
use Antares\Testing\TestCase;

class FactoryTest extends TestCase
{

    /**
     * @var \Antares\Widgets\Factory 
     */
    protected $stub;

    /**
     * @see inherit
     */
    public function setUp()
    {
        parent::setUp();


        $provider = m::mock('Antares\Memory\Provider');
        $provider->shouldReceive('get')->with('default')->andReturn(['name' => 'foo', 'path' => __DIR__ . '/Fixtures/Widgets/templates'])
                ->shouldReceive('put')->andReturnNull();

        $providerWidgets = m::mock('Antares\Memory\Provider');
        $providerWidgets->shouldReceive('all')
                ->withNoArgs()
                ->once()
                ->andReturn(['name' => 'foo'])
                ->shouldReceive('put')
                ->withAnyArgs()
                ->andReturnNull()
                ->shouldReceive('raw')
                ->withNoArgs()
                ->once()
                ->andReturn(['name' => 'foo']);


        $memory = m::mock('Antares\Widgets\Memory\WidgetHandler');
        $memory->shouldReceive('make')
                ->with('widgets-templates')
                ->andReturn($provider)
                ->shouldReceive('make')
                ->with('widgets')
                ->andReturn($providerWidgets);

        $this->app['antares.memory'] = $memory;

        $response   = m::mock('\Illuminate\Http\Response');
        $this->stub = new Stub($this->app, $response);
    }

    /**
     * Test Antares\Widgets\Factory::__construct() method.
     *
     * @test
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('Antares\Widgets\Factory', $this->stub);
    }

    /**
     * Test detect method
     * 
     * @test
     */
    public function testDetect()
    {
        $detection = $this->stub->detect();
        $this->assertTrue(is_array($detection));
        $this->assertTrue(!empty($detection));
    }

    /**
     * detect templates method
     * 
     * @test
     */
    public function testDetectTemplates()
    {
        $templates = $this->stub->detectTemplates();
        $this->assertTrue(is_array($templates));
        $isset     = isset($templates['default']);
        $this->assertTrue($isset);
        if ($isset) {
            $this->assertInstanceOf('Antares\Widgets\TemplateManifest', $templates['default']);
        }
    }

    /**
     * test widget finder
     * 
     * @test
     */
    public function testFinder()
    {
        $finder = $this->stub->finder();
        $this->assertInstanceOf('Antares\Widgets\Finder', $finder);
    }

    /**
     * test widget template finder
     * 
     * @test
     */
    public function testTemplateFinder()
    {
        $finder = $this->stub->templateFinder();
        $this->assertInstanceOf('Antares\Widgets\TemplateFinder', $finder);
    }

    /**
     * test model method
     * 
     * @test
     */
    public function testModel()
    {
        $this->assertInstanceOf('Antares\Widgets\Repository\Widgets', $this->stub->model());
    }

}
