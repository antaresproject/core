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


namespace Antares\Foundation\TestCase;

use Mockery as m;
use Antares\Foundation\Foundation;
use Antares\Foundation\Application;
use Illuminate\Support\Facades\Facade;

class FoundationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Application instance.
     *
     * @var Illuminate\Foundation\Application
     */
    private $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $app = new Application(__DIR__);

        $app['antares.acl']       = m::mock('\Antares\Contracts\Authorization\Authorization');
        $app['antares.extension'] = m::mock('\Antares\Contracts\Extension\Factory');
        $app['antares.mail']      = m::mock('\Antares\Notifier\Mailer')->makePartial();
        $app['antares.memory']    = m::mock('\Antares\Memory\MemoryManager', [$app]);
        $app['antares.notifier']  = m::mock('\Antares\Notifier\NotifierManager', [$app]);
        $app['antares.widget']    = m::mock('\Antares\Widget\Handlers\Menu');
        $app['config']            = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['events']            = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app['translator']        = m::mock('\Illuminate\Translation\Translator')->makePartial();
        $app['url']               = m::mock('\Illuminate\Routing\UrlGenerator')->makePartial();

        Facade::clearResolvedInstances();
        Application::setInstance($app);

        $this->app = $app;
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
     * Get installable mocks setup.
     *
     * @return \Mockery
     */
    private function getInstallableContainerSetup()
    {
        $app        = $this->app;
        $request    = m::mock('\Illuminate\Http\Request');
        $acl        = $app['antares.acl'];
        $config     = $app['config'];
        $event      = $app['events'];
        $mailer     = $app['antares.mail'];
        $memory     = $app['antares.memory'];
        $notifier   = $app['antares.notifier'];
        $translator = $app['translator'];
        $widget     = $app['antares.widget'];

        $app['env']               = 'production';
        $app['antares.installed'] = false;
        $app['request']           = $request;

        $memoryProvider = m::mock('\Antares\Contracts\Memory\Provider');

        $memoryProvider->shouldReceive('get')->once()->with('site.name')->andReturn('Antares');

        $acl->shouldReceive('make')->once()->andReturn($acl)
                ->shouldReceive('attach')->once()->with($memoryProvider)->andReturn($acl);
        $mailer->shouldReceive('attach')->once()->with($memoryProvider)->andReturnNull();
        $memory->shouldReceive('make')->once()->andReturn($memoryProvider);
        $notifier->shouldReceive('setDefaultDriver')->once()->with('antares')->andReturnNull();
        $widget->shouldReceive('make')->once()->with('menu.antares')->andReturn($widget)
                ->shouldReceive('make')->once()->with('menu.app')->andReturn($widget)
                ->shouldReceive('add->title->link')->once()->andReturnNull();
        $translator->shouldReceive('get')->andReturn('foo');
        $event->shouldReceive('listen')->once()
                ->with('antares.started: admin', 'Antares\Foundation\Http\Handlers\UserMenuHandler')->andReturnNull()
                ->shouldReceive('listen')->once()
                ->with('antares.started: admin', 'Antares\Foundation\Http\Handlers\ExtensionMenuHandler')->andReturnNull()
                ->shouldReceive('listen')->once()
                ->with('antares.started: admin', 'Antares\Foundation\Http\Handlers\SettingMenuHandler')->andReturnNull()
                ->shouldReceive('listen')->once()
                ->with('antares.started: admin', 'Antares\Foundation\Http\Handlers\ResourcesMenuHandler')->andReturnNull()
                ->shouldReceive('listen')->once()
                ->with('antares.ready: admin', 'Antares\Foundation\AdminMenuHandler')->andReturnNull()
                ->shouldReceive('fire')->once()->with('antares.started', [$memoryProvider])->andReturnNull();
        $config->shouldReceive('get')->once()->with('antares/foundation::handles', '/')->andReturn('admin');
        $request->shouldReceive('root')->andReturn('http://localhost')
                ->shouldReceive('secure')->andReturn(false);

        return $app;
    }

    /**
     * Get un-installable mocks setup.
     *
     * @return \Mockery
     */
    private function getUnInstallableContainerSetup()
    {
        $app      = $this->app;
        $request  = m::mock('\Illuminate\Http\Request');
        $acl      = $app['antares.acl'];
        $config   = $app['config'];
        $event    = $app['events'];
        $mailer   = $app['antares.mail'];
        $memory   = $app['antares.memory'];
        $notifier = $app['antares.notifier'];
        $widget   = $app['antares.widget'];

        $app['env']               = 'production';
        $app['request']           = $request;
        $app['antares.installed'] = false;

        $memoryProvider = m::mock('\Antares\Contracts\Memory\Provider');

        $memoryProvider->shouldReceive('get')->once()->with('site.name')->andReturnNull()
                ->shouldReceive('put')->once()->with('site.name', 'Antares')->andReturnNull();

        $acl->shouldReceive('make')->once()->andReturn($acl);
        $mailer->shouldReceive('attach')->once()->with($memoryProvider)->andReturnNull();
        $memory->shouldReceive('make')->once()->andReturn($memoryProvider)
                ->shouldReceive('make')->once()->with('runtime.antares')->andReturn($memoryProvider);
        $notifier->shouldReceive('setDefaultDriver')->once()->with('antares')->andReturnNull();
        $widget->shouldReceive('make')->once()->with('menu.antares')->andReturn($widget)
                ->shouldReceive('make')->once()->with('menu.app')->andReturn($widget)
                ->shouldReceive('add->title->link')->once()->with('http://localhost/admin/install')->andReturn($widget);
        $request->shouldReceive('root')->andReturn('http://localhost')
                ->shouldReceive('secure')->andReturn(false);
        $config->shouldReceive('get')->once()->with('antares/foundation::handles', '/')->andReturn('admin');
        $event->shouldReceive('fire')->once()->with('antares.started', [$memoryProvider])->andReturnNull();

        return $app;
    }

    /**
     * Test Antares\Foundation\Foundation::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $app  = $this->getInstallableContainerSetup();
        $stub = new Foundation($app);
        $stub->boot();

        $this->assertTrue($app['antares.installed']);
        $this->assertEquals($app['antares.widget'], $stub->menu());
        $this->assertEquals($app['antares.acl'], $stub->acl());
        $this->assertNotEquals($app['antares.memory'], $stub->memory());
        $this->assertEquals($stub, $stub->boot());
        $this->assertTrue($app['antares.installed']);
        $this->assertTrue($stub->installed());
    }

    /**
     * Test Antares\Foundation\Foundation::boot() method when database
     * is not installed yet.
     *
     * @test
     */
    public function testBootMethodWhenDatabaseIsNotInstalled()
    {
        $app = $this->getUnInstallableContainerSetup();

        $stub = new Foundation($app);
        $stub->boot();

        $this->assertFalse($app['antares.installed']);
        $this->assertFalse($stub->installed());
    }

    /**
     * Test Antares\Foundation\RouteManager::handles() method.
     *
     * @test
     */
    public function testHandlesMethod()
    {
        $app       = $this->app;
        $config    = $app['config'];
        $extension = $app['antares.extension'];
        $url       = $app['url'];

        $app['request'] = $request        = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->andReturn('http://localhost')
                ->shouldReceive('secure')->andReturn(false);

        $appRoute = m::mock('\Antares\Contracts\Extension\RouteGenerator');

        $config->shouldReceive('get')->once()
                ->with('antares/foundation::handles', '/')->andReturn('admin');

        $appRoute->shouldReceive('to')->once()->with('/')->andReturn('/')
                ->shouldReceive('to')->once()->with('info?foo=bar')->andReturn('info?foo=bar');
        $extension->shouldReceive('route')->once()->with('app', '/')->andReturn($appRoute);
        $url->shouldReceive('to')->once()->with('/')->andReturn('/')
                ->shouldReceive('to')->once()->with('info?foo=bar')->andReturn('info?foo=bar');

        $stub = new StubRouteManager($app);

        $this->assertEquals('/', $stub->handles('app::/'));
        $this->assertEquals('info?foo=bar', $stub->handles('info?foo=bar'));
        $this->assertEquals('http://localhost/admin/installer', $stub->handles('antares::installer'));
        $this->assertEquals('http://localhost/admin/installer', $stub->handles('antares::installer/'));
    }

    /**
     * Test Antares\Foundation\Foundation::is() method.
     *
     * @test
     */
    public function testIsMethod()
    {
        $app       = $this->app;
        $config    = $app['config'];
        $extension = $app['antares.extension'];

        $app['request'] = $request        = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->andReturn('http://localhost')
                ->shouldReceive('secure')->andReturn(false);

        $appRoute = m::mock('\Antares\Contracts\Extension\RouteGenerator');

        $config->shouldReceive('get')->once()
                ->with('antares/foundation::handles', '/')->andReturn('admin');
        $request->shouldReceive('path')->twice()->andReturn('/');
        $appRoute->shouldReceive('is')->once()->with('/')->andReturn(true)
                ->shouldReceive('is')->once()->with('info?foo=bar')->andReturn(true);
        $extension->shouldReceive('route')->once()->with('app', '/')->andReturn($appRoute);

        $stub = new StubRouteManager($app);

        $this->assertTrue($stub->is('app::/'));
        $this->assertTrue($stub->is('info?foo=bar'));
        $this->assertFalse($stub->is('antares::login'));
        $this->assertFalse($stub->is('antares::login'));
    }

    /**
     * Test Antares\Foundation\RouteManager::namespaced() method.
     *
     * @test
     */
    public function testNamespacedMethod()
    {
        $stub = m::mock('\Antares\Foundation\Foundation[group]', [$this->app]);

        $closure = function () {
            
        };

        $middleware = ['Antares\Foundation\Http\Middleware\UseBackendTheme'];

        $stub->shouldReceive('group')->times(3)
                ->with('antares/foundation', 'antares', ['middleware' => $middleware], $closure)
                ->andReturn([]);
        $stub->shouldReceive('group')->once()
                ->with('antares/foundation', 'antares', ['namespace' => 'Foo', 'middleware' => $middleware], $closure)
                ->andReturn([]);

        $this->assertNull($stub->namespaced('', $closure));
        $this->assertNull($stub->namespaced('\\', $closure));
        $this->assertNull($stub->namespaced(null, $closure));
        $this->assertNull($stub->namespaced('Foo', $closure));
    }

}

class StubRouteManager extends Foundation
{

    public function boot()
    {
        
    }

}
