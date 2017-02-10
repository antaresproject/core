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
 namespace Antares\Publisher\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Publisher\AssetManager;

class AssetManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();
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
     * Test Antares\Publisher\AssetManager::publish() method.
     *
     * @test
     */
    public function testPublishMethod()
    {
        $publisher = m::mock('\Antares\Publisher\Publishing\AssetPublisher');
        $publisher->shouldReceive('publish')->once()->with('foo', 'bar')->andReturn(true);

        $stub = new AssetManager($this->app, $publisher);
        $this->assertTrue($stub->publish('foo', 'bar'));
    }

    /**
     * Test Antares\Publisher\AssetManager::extension() method.
     *
     * @test
     */
    public function testExtensionMethod()
    {
        $app                               = $this->app;
        $app['files']                      = $files                      = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['antares.extension']        = $extension        = m::mock('\Antares\Extension\Factory');
        $app['antares.extension.finder'] = $finder = m::mock('\Antares\Extension\Finder');

        $publisher = m::mock('\Antares\Publisher\Publishing\AssetPublisher');

        $files->shouldReceive('isDirectory')->once()->with('bar/resources/public')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('bar/public')->andReturn(true)
            ->shouldReceive('isDirectory')->once()->with('foobar/public')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('foobar/resources/public')->andReturn(false);
        $extension->shouldReceive('option')->once()->with('foo', 'path')->andReturn('bar')
            ->shouldReceive('option')->once()->with('foobar', 'path')->andReturn('foobar');
        $finder->shouldReceive('resolveExtensionPath')->once()->with('bar')->andReturn('bar')
            ->shouldReceive('resolveExtensionPath')->once()->with('foobar')->andReturn('foobar');
        $publisher->shouldReceive('publish')->once()->with('foo', 'bar/public')->andReturn(true);

        $stub = new AssetManager($app, $publisher);
        $this->assertTrue($stub->extension('foo'));
        $this->assertFalse($stub->extension('foobar'));
    }

    /**
     * Test Antares\Publisher\AssetManager::extension() method
     * throws exception.
     *
     * @expectedException \Antares\Contracts\Publisher\FilePermissionException
     */
    public function testExtensionMethodThrowsException()
    {
        $app                               = $this->app;
        $app['files']                      = $files                      = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['antares.extension']        = $extension        = m::mock('\Antares\Extension\Factory');
        $app['antares.extension.finder'] = $finder = m::mock('\Antares\Extension\Finder');

        $publisher = m::mock('\Antares\Publisher\Publishing\AssetPublisher');

        $files->shouldReceive('isDirectory')->once()->with('bar/resources/public')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with('bar/public')->andReturn(true);
        $extension->shouldReceive('option')->once()->with('foo', 'path')->andReturn('bar');
        $finder->shouldReceive('resolveExtensionPath')->once()->with("bar")->andReturn('bar');
        $publisher->shouldReceive('publish')->once()->with('foo', 'bar/public')->andThrow('\Exception');

        $stub = new AssetManager($app, $publisher);
        $this->assertFalse($stub->extension('foo'));
    }

    /**
     * Test Antares\Publisher\AssetManager::foundation() method.
     *
     * @test
     */
    public function testFoundationMethod()
    {
        $app              = $this->app;
        $app['files']     = $files     = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['path.base'] = '/foo/path/';

        $publisher = m::mock('\Antares\Publisher\Publishing\AssetPublisher');

        $files->shouldReceive('isDirectory')->once()
            ->with('/foo/path/vendor/antares/foundation/resources/public')->andReturn(true);
        $publisher->shouldReceive('publish')->once()
            ->with('antares/foundation', '/foo/path/vendor/antares/foundation/resources/public')->andReturn(true);

        $stub = new AssetManager($app, $publisher);
        $this->assertTrue($stub->foundation());
    }

    /**
     * Test Antares\Publisher\AssetManager::foundation() method
     * when public directory does not exists.
     *
     * @test
     */
    public function testFoundationMethodWhenPublicDirectoryDoesNotExists()
    {
        $app              = $this->app;
        $app['files']     = $files     = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['path.base'] = '/foo/path/';

        $publisher = m::mock('\Antares\Publisher\Publishing\AssetPublisher');

        $files->shouldReceive('isDirectory')->once()
            ->with('/foo/path/vendor/antares/foundation/resources/public')->andReturn(false);

        $stub = new AssetManager($app, $publisher);
        $this->assertFalse($stub->foundation());
    }

    /**
     * Test Antares\Publisher\AssetManager::foundation() method
     * throws an exception.
     *
     * @expectedException \Antares\Contracts\Publisher\FilePermissionException
     */
    public function testFoundationMethodThrowsException()
    {
        $app              = $this->app;
        $app['files']     = $files     = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['path.base'] = '/foo/path/';

        $publisher = m::mock('\Antares\Publisher\Publishing\AssetPublisher');

        $files->shouldReceive('isDirectory')->once()
            ->with('/foo/path/vendor/antares/foundation/resources/public')->andReturn(true);
        $publisher->shouldReceive('publish')->once()
            ->with('antares/foundation', '/foo/path/vendor/antares/foundation/resources/public')->andThrow('Exception');

        $stub = new AssetManager($app, $publisher);
        $stub->foundation();
    }
}
