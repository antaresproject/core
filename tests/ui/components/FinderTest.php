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

use Antares\Widgets\Tests\Fixtures\Widgets\WidgetTest;
use Illuminate\Support\Collection;
use Antares\Testing\TestCase;
use Antares\Memory\Provider;
use Antares\Widgets\Finder;
use Mockery as m;

class FinderTest extends TestCase
{

    /**
     * @var Finder
     */
    protected $stub;

    /**
     * @see inherited
     */
    public function setUp()
    {
        spl_autoload_register(function ($class) {
            if ($class == WidgetTest::class) {
                include __DIR__ . '/Fixtures/Widgets/WidgetTest.php';
            }
        });

        parent::setUp();


        $fileSystem = m::mock('Illuminate\Filesystem\Filesystem');
        $fileSystem->shouldReceive('directories')->andReturn([
                    __DIR__ . '/Fixtures/Widgets'
                ])->shouldReceive('files')->andReturn([
                    __DIR__ . '/Fixtures/Widgets/WidgetTest.php'
                ])->shouldReceive('name')->andReturn('WidgetTest')
                ->shouldReceive('get')->andReturn(file_get_contents(__DIR__ . '/Fixtures/Widgets/WidgetTest.php'));
        $config     = [
            'path.app'  => app('path'),
            'path.base' => app('path.base')
        ];



        $this->stub = new Finder($fileSystem, $config);

        $provider = m::mock(Provider::class);
        $provider->shouldReceive('get')
                ->with('default')
                ->andReturn(['name' => 'foo', 'path' => __DIR__ . '/Fixtures/Widgets/templates'])
                ->shouldReceive('put')
                ->andReturnNull();

        $providerWidgets = m::mock(Provider::class);
        $providerWidgets->shouldReceive('all')->withNoArgs()->once()->andReturn(['name' => 'foo'])
                ->shouldReceive('put')->withAnyArgs()->andReturnNull()
                ->shouldReceive('raw')->withNoArgs()->once()->andReturn(['name' => 'foo'])
                ->shouldReceive('get')->with("extensions.active", [])->once()->andReturn([['name' => 'foo', 'path' => __DIR__]]);


        $memory = m::mock(\Antares\Widgets\Memory\WidgetHandler::class);
        $memory->shouldReceive('make')->with('widgets-templates')->once()->andReturn($provider)
                ->shouldReceive('make')->with('widgets')->once()->andReturn($providerWidgets)
                ->shouldReceive('make')->with('component')->once()->andReturn($providerWidgets);

        $this->app['antares.memory'] = $memory;
    }

    /**
     * test constructing
     * 
     * @test
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(Finder::class, $this->stub);
    }

    /**
     * test add path method
     * 
     * @test
     */
    public function testAddPath()
    {
        $this->assertInstanceOf(Finder::class, $this->stub->addPath('/'));
    }

    /**
     * test detect
     * 
     * @test
     */
    public function testDetect()
    {
        $detection = $this->stub->detect();
        $this->assertInstanceOf(Collection::class, $detection);
        $this->assertNull($detection->first());
    }

    /**
     * test detect routes
     * 
     * @test
     */
    public function testDetectRoutes()
    {
        $detection = $this->stub->detectRoutes();
        $this->assertInstanceOf(Collection::class, $detection);
        $first     = $detection->first();
        $this->assertNull($first);
    }

    /**
     * test resolve widget path
     * 
     * @test
     */
    public function testResolveWidgetPath()
    {
        $this->assertSame($this->app['path.base'] . '/src/antares/components/widgets', $this->stub->resolveWidgetPath('vendor::antares/components/widgets'));
    }

}
