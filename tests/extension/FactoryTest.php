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


namespace Antares\Extension\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Antares\Extension\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app = null;

    /**
     * Dispatcher instance.
     *
     * @var \Antares\Extension\Dispatcher
     */
    protected $dispatcher = null;

    /**
     * Debugger (safe mode) instance.
     *
     * @var \Antares\Extension\SafeMode
     */
    protected $debugger = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app        = new Container();
        $this->dispatcher = m::mock('\Antares\Extension\Dispatcher');
        $this->debugger   = m::mock('\Antares\Extension\SafeModeChecker');
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        unset($this->dispatcher);
        unset($this->debugger);
        m::close();
    }

    /**
     * Get data provider.
     */
    protected function dataProvider()
    {
        return [
            [
                'path'    => '/foo/path/laravel/framework/',
                'config'  => ['foo' => 'bar', 'handles' => 'laravel'],
                'provide' => ['Laravel\FrameworkServiceProvider'],
            ],
            [
                'path'    => '/foo/app/',
                'config'  => ['foo' => 'bar'],
                'provide' => [],
            ],
        ];
    }

    /**
     * Test Antares\Extension\Factory::available() method.
     *
     * @test
     */
    public function testAvailableMethod()
    {
        $app    = $this->app;
        $memory = m::mock('\Antares\Contracts\Memory\Provider');

        $app['antares.memory'] = $memory;

        $memory->shouldReceive('get')
                ->once()->with('extensions.available.laravel/framework')->andReturn([]);

        $stub = new Factory($app, $this->dispatcher, $this->debugger);
        $stub->attach($memory);
        $this->assertTrue($stub->available('laravel/framework'));
    }

    /**
     * Test Antares\Extension\Factory::active() method.
     *
     * @test
     */
    public function testActivateMethod()
    {
        $app        = $this->app;
        $dispatcher = $this->dispatcher;

        $memory   = m::mock('\Antares\Contracts\Memory\Provider');
        $migrator = m::mock('\Antares\Publisher\MigrateManager');
        $asset    = m::mock('\Antares\Publisher\AssetManager');
        $events   = m::mock('\Illuminate\Contracts\Events\Dispatcher');

        $app['antares.memory']            = $memory;
        $app['antares.publisher.migrate'] = $migrator;
        $app['antares.publisher.asset']   = $asset;
        $app['events']                    = $events;

        $dispatcher->shouldReceive('register')->once()->with('laravel/framework', m::type('Array'))->andReturnNull();
        $memory->shouldReceive('get')->twice()
                ->with('extensions.available', [])->andReturn(['laravel/framework' => []])
                ->shouldReceive('get')->twice()->with('extensions.active', [])->andReturn([])
                ->shouldReceive('put')->once()
                ->with('extensions.active', ['laravel/framework' => []])->andReturn(true);
        $migrator->shouldReceive('extension')->once()->with('laravel/framework')->andReturn(true);
        $asset->shouldReceive('extension')->once()->with('laravel/framework')->andReturn(true);
        $events->shouldReceive('fire')->once()
                ->with('antares.publishing', ['laravel/framework'])->andReturn(true)
                ->shouldReceive('fire')->once()
                ->with('antares.publishing: laravel/framework')->andReturn(true)
                ->shouldReceive('fire')->once()
                ->with('antares.activating: laravel/framework', ['laravel/framework'])
                ->andReturnNull();

        $stub = new Factory($app, $dispatcher, $this->debugger);
        $stub->attach($memory);
                    }

    /**
     * Test Antares\Extension\Factory::activated() method.
     *
     * @test
     */
    public function testActivatedMethod()
    {
        $app    = $this->app;
        $memory = m::mock('\Antares\Contracts\Memory\Provider');

        $app['antares.memory'] = $memory;

        $memory->shouldReceive('get')->once()->with('extensions.active.laravel/framework')->andReturn([]);

        $stub = new Factory($app, $this->dispatcher, $this->debugger);
        $stub->attach($memory);
        $this->assertTrue($stub->activated('laravel/framework'));
    }

    /**
     * Test Antares\Extension\Factory::deactive() method.
     *
     * @test
     */
    public function testDeactivateMethod()
    {
        $app                   = $this->app;
        $app['antares.memory'] = $memory                = m::mock('\Antares\Contracts\Memory\Provider');
        $app['events']         = $events                = m::mock('\Illuminate\Contracts\Events\Dispatcher[fire]');

        $memory->shouldReceive('get')->twice()
                ->with('extensions.active', [])
                ->andReturn(['laravel/framework' => [], 'daylerees/doc-reader' => []])
                ->shouldReceive('put')->once()
                ->with('extensions.active', ['daylerees/doc-reader' => []])
                ->andReturn(true);
        $events->shouldReceive('fire')->once()
                ->with('antares.deactivating: laravel/framework', ['laravel/framework'])
                ->andReturnNull();

        $stub = new Factory($app, $this->dispatcher, $this->debugger);
        $stub->attach($memory);
            }

    /**
     * Test Antares\Extension\Factory::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $app        = $this->app;
        $dispatcher = $this->dispatcher;
        $debugger   = $this->debugger;
        $events     = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $memory     = m::mock('\Antares\Contracts\Memory\Provider');

        $app['antares.memory'] = $memory;
        $app['events']         = $events;

        list($options1, $options2) = $this->dataProvider();

        $extension = ['laravel/framework' => $options1, 'app' => $options2];

        $events->shouldReceive('fire')->once()->with('antares.extension: booted')->andReturnNull();
        $memory->shouldReceive('get')->once()->with('extensions.available', [])->andReturn($extension)
                ->shouldReceive('get')->once()->with('extensions.active', [])->andReturn($extension);
        $dispatcher->shouldReceive('register')->once()->with('laravel/framework', $options1)->andReturnNull()
                ->shouldReceive('register')->once()->with('app', $options2)->andReturnNull()
                ->shouldReceive('boot')->once()->andReturnNull();
        $debugger->shouldReceive('check')->once()->andReturn(false);

        $stub = new Factory($app, $dispatcher, $debugger);
        $stub->attach($memory);

        $this->assertEquals($memory, $stub->getMemoryProvider());

        $stub->boot();

        $this->assertEquals($options1['config'], $stub->option('laravel/framework', 'config'));
        $this->assertEquals('bad!', $stub->option('foobar/hello-world', 'config', 'bad!'));
                $this->assertFalse($stub->started('foobar/hello-world'));
    }

    /**
     * Test Antares\Extension\Factory::detect() method.
     *
     * @test
     */
    public function testDetectMethod()
    {
        $app    = $this->app;
        $events = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $finder = m::mock('\Antares\Contracts\Extension\Finder');
        $memory = m::mock('\Antares\Contracts\Memory\Provider');

        $app['events']                   = $events;
        $app['antares.extension.finder'] = $finder;
        $app['antares.memory']           = $memory;

        $extensions = new Collection(['foo']);

        $events->shouldReceive('fire')->once()->with('antares.extension: detecting')->andReturnNull();
        $finder->shouldReceive('detect')->once()->andReturn($extensions);
        $memory->shouldReceive('put')->once()->with('extensions.available', ['foo'])->andReturn('foobar');

        $stub = new Factory($app, $this->dispatcher, $this->debugger);
        $stub->attach($memory);
        $this->assertEquals($extensions, $stub->detect());
    }

    /**
     * Test Antares\Extension\Factory::finder() method.
     *
     * @test
     */
    public function testFinderMethod()
    {
        $app    = $this->app;
        $finder = m::mock('\Antares\Contracts\Extension\Finder');

        $app['antares.extension.finder'] = $finder;

        $stub = new Factory($app, $this->dispatcher, $this->debugger);

        $this->assertEquals($finder, $stub->finder());
    }

    /**
     * Test Antares\Extension\Factory::finish() method.
     *
     * @test
     */
    public function testFinishMethod()
    {
        $dispatcher = $this->dispatcher;

        list($options1, $options2) = $this->dataProvider();

        $dispatcher->shouldReceive('finish')->with('laravel/framework', $options1)->andReturnNull()
                ->shouldReceive('finish')->with('app', $options2)->andReturnNull();

        $stub = new Factory($this->app, $dispatcher, $this->debugger);

        $refl       = new \ReflectionObject($stub);
        $extensions = $refl->getProperty('extensions');
        $extensions->setAccessible(true);
        $extensions->setValue($stub, ['laravel/framework' => $options1, 'app' => $options2]);

        $stub->finish();
    }

    /**
     * Test Antares\Extension\Factory::permission() method.
     *
     * @test
     */
    public function testPermissionMethod()
    {
        $app    = $this->app;
        $memory = m::mock('\Antares\Contracts\Memory\Provider');
        $finder = m::mock('Finder');
        $files  = m::mock('Filesystem');

        $app['path.public']              = '/var/antares';
        $app['antares.memory']           = $memory;
        $app['files']                    = $files;
        $app['antares.extension.finder'] = $finder;

        $memory->shouldReceive('get')->once()->with('extensions.available.foo.path', 'foo')->andReturn('foo')
                ->shouldReceive('get')->once()->with('extensions.available.bar.path', 'bar')->andReturn('bar')
                ->shouldReceive('get')->once()->with('extensions.available.laravel/framework.path', 'laravel/framework')->andReturn('laravel/framework');
        $finder->shouldReceive('resolveExtensionPath')->once()->with('foo/public')->andReturn('foo/public')
                ->shouldReceive('resolveExtensionPath')->once()->with('bar/public')->andReturn('bar/public')
                ->shouldReceive('resolveExtensionPath')->once()->with('laravel/framework/public')->andReturn('laravel/framework/public');
        $files->shouldReceive('isDirectory')->once()->with('foo/public')->andReturn(false)
                ->shouldReceive('isWritable')->once()->with('/var/antares/packages/foo')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('bar/public')->andReturn(true)
                ->shouldReceive('isWritable')->once()->with('/var/antares/packages/bar')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('laravel/framework/public')->andReturn(true)
                ->shouldReceive('isDirectory')->once()->with('/var/antares/packages/laravel/framework')->andReturn(false)
                ->shouldReceive('isWritable')->once()->with('/var/antares/packages/laravel')->andReturn(true);

        $stub = new Factory($app, $this->dispatcher, $this->debugger);
        $stub->attach($memory);
        $this->assertTrue($stub->permission('foo'));
        $this->assertFalse($stub->permission('bar'));
        $this->assertTrue($stub->permission('laravel/framework'));
    }

    /**
     * Test Antares\Extension\Factory::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app    = $this->app;
        $finder = m::mock('\Antares\Contracts\Extension\Finder');
        $memory = m::mock('\Antares\Contracts\Memory\Provider');

        $app['antares.extension.finder'] = $finder;
        $app['antares.memory']           = $memory;

        $stub = new Factory($app, $this->dispatcher, $this->debugger);

        $finder->shouldReceive('registerExtension')->once()->with('hello', '/path/hello')->andReturn(true);

        $this->assertTrue($stub->register('hello', '/path/hello'));
    }

    /**
     * Test Antares\Extension\Factory::reset() method.
     *
     * @test
     */
    public function testResetMethod()
    {
        $app    = $this->app;
        $memory = m::mock('\Antares\Contracts\Memory\Provider');

        $app['antares.memory'] = $memory;
        $extension             = ['config' => ['handles' => 'app']];

        $memory->shouldReceive('get')->once()
                ->with('extensions.available.laravel/framework', [])->andReturn($extension)
                ->shouldReceive('put')->once()
                ->with('extensions.active.laravel/framework', $extension)->andReturnNull()
                ->shouldReceive('has')->once()
                ->with('extension_laravel/framework')->andReturn(true)
                ->shouldReceive('put')->once()
                ->with('extension_laravel/framework', [])->andReturnNull();

        $stub = new Factory($app, $this->dispatcher, $this->debugger);
        $stub->attach($memory);
        $this->assertTrue($stub->reset('laravel/framework'));
    }

    /**
     * Test Antares\Extension\Factory::route() method.
     *
     * @test
     */
    public function testRouteMethod()
    {
        $app        = $this->app;
        $dispatcher = $this->dispatcher;
        $debugger   = $this->debugger;
        $events     = m::mock('\Antares\Contracts\Events\Dispatcher');
        $memory     = m::mock('\Antares\Contracts\Memory\Provider');
        $config     = m::mock('\Illuminate\Contracts\Config\Repository');
        $request    = m::mock('\Illuminate\Http\Request');

        $app['antares.memory'] = $memory;
        $app['events']         = $events;
        $app['config']         = $config;
        $app['request']        = $request;

        list($options1, $options2) = $this->dataProvider();

        $extension = ['laravel/framework' => $options1, 'app' => $options2];

        $events->shouldReceive('fire')->once()->with('antares.extension: booted')->andReturnNull();
        $memory->shouldReceive('get')->once()->with('extensions.available', [])->andReturn($extension)
                ->shouldReceive('get')->once()->with('extensions.active', [])->andReturn($extension);
        $dispatcher->shouldReceive('register')->once()->with('laravel/framework', $options1)->andReturnNull()
                ->shouldReceive('register')->once()->with('app', $options2)->andReturnNull()
                ->shouldReceive('boot')->once()->andReturnNull();
        $debugger->shouldReceive('check')->once()->andReturn(false);
        $config->shouldReceive('get')->with('antares/extension::handles.laravel/framework', '/')->andReturn('laravel');
        $request->shouldReceive('root')->once()->andReturn('http://localhost')
                ->shouldReceive('secure')->twice()->andReturn(false);

        $stub = new Factory($app, $dispatcher, $debugger);
        $stub->attach($memory);
        $stub->boot();

        $output = $stub->route('laravel/framework', '/');

        $this->assertInstanceOf('\Antares\Extension\RouteGenerator', $output);
        $this->assertEquals('laravel', $output);
        $this->assertEquals(null, $output->domain());
        $this->assertEquals('localhost', $output->domain(true));
        $this->assertEquals('laravel', $output->prefix());
        $this->assertEquals('laravel', $output->prefix(true));
        $this->assertEquals('http://localhost/laravel', $output->root());
        $this->assertEquals('http://localhost/laravel/hello', $output->to('hello'));
    }

}
