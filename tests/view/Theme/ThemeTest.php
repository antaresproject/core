<?php

/**
 * Part of the Antares Project package.
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
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\View\TestCase\Theme;

use Illuminate\Container\Container;
use Mockery as m;
use Antares\View\Theme\Theme;

class ThemeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    private $app;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();

        $this->app['path.public'] = '/var/antares/public';
        $this->app['path.base']   = '/var/antares';
        
        $request->shouldReceive('root')->andReturn('http://localhost/');
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);

        m::close();
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
                ->shouldReceive('setPaths')->once()->with([$defaultPath])->andReturnNull()
                ->shouldReceive('setPaths')->once()->with(["{$resourcePath}/foo", "{$themePath}/foo", $defaultPath])->andReturnNull()
                ->shouldReceive('setPaths')->once()->with(["{$resourcePath}/default", "{$themePath}/default", $defaultPath])->andReturnNull();
        $files->shouldReceive('isDirectory')->once()->with("{$themePath}/foo")->andReturn(true)
                ->shouldReceive('isDirectory')->once()->with("{$resourcePath}/foo")->andReturn(true)
                ->shouldReceive('isDirectory')->once()->with("{$themePath}/default")->andReturn(true)
                ->shouldReceive('isDirectory')->once()->with("{$resourcePath}/default")->andReturn(true)
                ->shouldReceive('exists')->once()->with("{$themePath}/default/theme.json")->andReturn(true)
                ->shouldReceive('get')->once()->with('/var/antares/public/themes/default/theme.json')
                ->andReturn('{"autoload":["start.php"]}')
                ->shouldReceive('requireOnce')->once()->with('/var/antares/public/themes/default/start.php')
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

        $themePath    = '/var/antares/public/themes';
        $resourcePath = '/var/antares/resources/themes';

        $stub = new Theme($app, $events, $files);

        $files->shouldReceive('exists')->once()->with("{$themePath}/default/theme.json")->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with("{$themePath}/default")->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with("{$resourcePath}/default")->andReturn(false);

        $events->shouldReceive('fire')->once()->with('antares.theme.resolving', m::type('Array'))->andReturnNull()
                ->shouldReceive('fire')->once()->with('antares.theme.set: default')->andReturnNull()
                ->shouldReceive('fire')->once()->with('antares.theme.boot: default')->andReturnNull();

        $stub->initiate();

        $stub->setTheme('default');

        $this->assertTrue($stub->resolving());

        $stub->boot();
    }

}
