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

namespace Antares\View\TestCase\Theme;

use Antares\Testbench\ApplicationTestCase;
use Antares\View\Theme\Theme;
use Mockery as m;

class ThemeTest extends ApplicationTestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $this->app['path.public'] = '/var/antares/public';
        $this->app['path.base']   = '/var/antares';
    }

    /**
     * Test Antares\View\Theme\Container::setTheme() and
     * Antares\View\Theme\Container::getTheme() method.
     *
     * @test
     */
    public function testGetterAndSetterForTheme()
    {
        $app                = $this->app;
        $app['view.finder'] = $finder             = m::mock('\Antares\View\FileViewFinder');
        $app['files']       = $files              = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['events']      = $events             = m::mock('\Illuminate\Contracts\Events\Dispatcher');

        $defaultPath  = '/var/antares/resources/views';
        $themePath    = '/var/antares/public/themes';
        $resourcePath = '/var/antares/resources/themes';

        $stub = new Theme($app, $events, $files);

        $finder->shouldReceive('getPaths')->times(3)->andReturn([$defaultPath])
                ->shouldReceive('setPaths')->with([$defaultPath])->andReturnNull()
                ->shouldReceive('setPaths')->with(["{$resourcePath}/foo", "{$themePath}/foo", $defaultPath])->andReturnNull()
                ->shouldReceive('setPaths')->with(["/foo", "/foo", $defaultPath,])->andReturnNull()
                ->shouldReceive('setPaths')->with(["/default", "/default", $defaultPath,])->andReturnNull()
                ->shouldReceive('setPaths')->with(["{$resourcePath}/default", "{$themePath}/default", $defaultPath])->andReturnNull();

        $files->shouldReceive('isDirectory')->with("/default")->andReturn(true);

        $files
                ->shouldReceive('isDirectory')->twice()->with("/foo")->andReturn(true)
                ->shouldReceive('exists')->with("/default/theme.json")->andReturn(true)
                ->shouldReceive('get')->once()->with('/default/theme.json')
                ->andReturn('{"autoload":["start.php"]}')
                ->shouldReceive('requireOnce')->once()->with('/default/start.php')
                ->andReturnNull();

        $events->shouldReceive('fire')->twice()->with('antares.theme.resolving', [$stub, $app])->andReturnNull()
                ->shouldReceive('fire')->once()->with('antares.theme.set: foo')->andReturnNull()
                ->shouldReceive('fire')->once()->with('antares.theme.unset: foo')->andReturnNull()
                ->shouldReceive('fire')->once()->with('antares.theme.set: default')->andReturnNull()
                ->shouldReceive('fire')->once()->with('antares.theme.boot: default')->andReturnNull();

        $stub->initiate();

        $stub->setTheme('foo');

        $this->assertEquals('foo', $stub->getTheme());

        $this->assertTrue($stub->resolving());

        $stub->setTheme('default');

        $this->assertEquals('default', $stub->getTheme());

        $this->assertTrue($stub->boot());

        $this->assertEquals("http://localhost/themes/default/hello", $stub->to('hello'));
        $this->assertEquals("/themes/default/hello", $stub->asset('hello'));

        $this->assertFalse($stub->resolving());
        $this->assertFalse($stub->boot());
    }

    /**
     * Test Antares\View\Theme\Container::boot() method when manifest
     * is not available.
     *
     * @test
     */
    public function testBootMethodWhenManifestIsNotAvailable()
    {
        $app                = $this->app;
        $app['view.finder'] = $finder             = m::mock('\Antares\View\FileViewFinder');
        $app['events']      = $events             = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app['files']       = $files              = m::mock('\Illuminate\Filesystem\Filesystem');


        $stub = new Theme($app, $events, $files);

        $files->shouldReceive('exists')->once()->with("/default/theme.json")->andReturn(false)
                ->shouldReceive('isDirectory')->twice()->with("/default")->andReturn(false);

        $events->shouldReceive('fire')->once()->with('antares.theme.resolving', m::type('Array'))->andReturnNull()
                ->shouldReceive('fire')->once()->with('antares.theme.set: default')->andReturnNull()
                ->shouldReceive('fire')->once()->with('antares.theme.boot: default')->andReturnNull();

        $stub->initiate();

        $stub->setTheme('default');

        $this->assertTrue($stub->resolving());

        $stub->boot();
    }

}
