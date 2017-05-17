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

namespace Antares\Foundation\TestCase;

use Mockery as m;
use Antares\Foundation\Foundation;
use Antares\Testing\ApplicationTestCase;

class FoundationTest extends ApplicationTestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $memory = m::mock('\Antares\Contracts\Memory\Provider');
        $memory->shouldReceive('get')->with('email', [])->andReturn(['driver' => 'mail'])
                ->shouldReceive('get')->with('email.driver', 'mail')->andReturn('mail')
                ->shouldReceive('get')->with('email.from')->andReturn([
            'address' => 'hello@antaresplatform.com',
            'name'    => 'Antares Platform',
        ]);

        $transport                 = new \Antares\Notifier\TransportManager($this->app);
        $mailer                    = with(new \Antares\Notifier\Mailer($this->app, $transport))->attach($memory);
        $this->app['antares.mail'] = $mailer;
    }

    /**
     * Get installable mocks setup.
     *
     * @return \Mockery
     */
    private function getInstallableContainerSetup()
    {
        $app                      = $this->app;
        $request                  = m::mock('\Illuminate\Http\Request');
        $app['env']               = 'production';
        $app['antares.installed'] = false;
        $app['request']           = $request;


        return $app;
    }

    /**
     * Get un-installable mocks setup.
     *
     * @return \Mockery
     */
    private function getUnInstallableContainerSetup()
    {
        $app     = $this->app;
        $request = m::mock('\Illuminate\Http\Request');
        $widget  = $app['antares.widget'];

        $app['env']               = 'production';
        $app['request']           = $request;
        $app['antares.installed'] = false;
        $app['antares.widget']    = $widget;
        return $app;
    }

    /**
     * Test Antares\Foundation\Foundation::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $app = $this->getInstallableContainerSetup();

        $stub = new Foundation($app);
        $stub->boot();
        $this->assertTrue($app['antares.installed']);
        $this->assertInstanceOf(\Antares\UI\TemplateBase\Menu::class, $stub->menu());
        $this->assertInstanceOf(\Antares\Authorization\Authorization::class, $stub->acl());
        $this->assertNotEquals($app['antares.memory'], $stub->memory());
        $this->assertEquals($stub, $stub->boot());
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
        $app  = $this->getUnInstallableContainerSetup();
        $stub = new Foundation($app);
        $this->assertFalse($app['antares.installed']);
        $this->assertFalse($stub->installed());
        $stub->boot();
        $this->assertTrue($stub->installed());
    }

    /**
     * Test Antares\Foundation\RouteManager::handles() method.
     *
     * @test
     */
    public function testHandlesMethod()
    {
        $app = $this->app;
        $url = $app['url'];

        $app['request'] = $request        = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->andReturn('http://localhost')
                ->shouldReceive('secure')->andReturn(false)
                ->shouldReceive('segment')->andReturn(1)->getMock();


        $stub = new StubRouteManager($app);
        $this->assertEquals('http://localhost', $stub->handles('app::/'));
        $this->assertEquals('http://localhost/info?foo=bar', $stub->handles('info?foo=bar'));
        $this->assertEquals('http://localhost/antares/installer', $stub->handles('antares::installer'));
        $this->assertEquals('http://localhost/antares/installer', $stub->handles('antares::installer/'));
    }

    /**
     * Test Antares\Foundation\Foundation::is() method.
     *
     * @test
     */
    public function testIsMethod()
    {
        $app = $this->app;


        $app['request'] = $request        = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->andReturn('http://localhost')
                ->shouldReceive('secure')->andReturn(false)
                ->shouldReceive('segment')->andReturn(1)
                ->shouldReceive('path')->times(4)->andReturn('/')->getMock();


        $stub = new StubRouteManager($app);
        $this->assertTrue($stub->is('app::/'));
        $this->assertFalse($stub->is('info?foo=bar'));
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
        $stub    = new Foundation($this->app);
        $stub->boot();
        $closure = function () {
            
        };
        $this->assertNull($stub->namespaced('test', $closure));
    }

}

class StubRouteManager extends Foundation
{

    public function boot()
    {
        
    }

}
