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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications\Http\Handlers\TestCase;

use Antares\Notifications\Http\Handlers\Menu as NotificationsMenu;
use Antares\Foundation\Support\MenuHandler;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class MenuTest extends ApplicationTestCase
{

    /**
     * Tests Antares\Notifications\Http\Handlers\Menu::__construct
     * 
     * @test
     */
    public function testItIsInitializable()
    {
        $app  = m::mock('Illuminate\Container\Container');
        $menu = m::mock(Menu::class);
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $stub = new NotificationsMenu($app);
        $this->assertInstanceOf(NotificationsMenu::class, $stub);
        $this->assertInstanceOf(MenuHandler::class, $stub);
    }

    /**
     * Tests Antares\Notifications\Http\Handlers\Menu::getPositionAttribute
     * 
     * @test
     */
    public function testItShouldBeChildOfExtensionGivenExtensionIsAvailable()
    {
        $app  = m::mock('Illuminate\Container\Container');
        $menu = m::mock(Menu::class);
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $menu->shouldReceive('has')->once()->with('settings.brands')->andReturn(true);
        $stub = new NotificationsMenu($app);
        $this->assertEquals('>:settings.brands', $stub->getPositionAttribute());
    }

    /**
     * Tests Antares\Notifications\Http\Handlers\Menu::getPositionAttribute
     * 
     * @test
     */
    public function testItShouldNextToHomeGivenExtensionIsntAvailable()
    {
        $app  = m::mock('Illuminate\Container\Container');
        $menu = m::mock(Menu::class);
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $menu->shouldReceive('has')->once()->with('settings.brands')->andReturn(false);
        $stub = new NotificationsMenu($app);
        $this->assertEquals('>:settings.general-config', $stub->getPositionAttribute());
    }

    /**
     * Tests Antares\Notifications\Http\Handlers\Menu::authorize
     * 
     * @test
     */
    public function testAuthorize()
    {
        $this->app['antares.platform.memory'] = m::mock('Antares\Memory\Provider');
        $app                                  = m::mock('Illuminate\Container\Container');
        $menu                                 = m::mock(Menu::class);
        $acl                                  = m::mock('Antares\Authorization\Factory')
                ->shouldReceive('make')->with("antares/notifications")->andReturnSelf()
                ->shouldReceive("can")->with(m::type("String"))->andReturn(true)
                ->shouldReceive('attach')->with($this->app['antares.platform.memory'])->andReturnSelf()
                ->getMock();

        $this->app['antares.acl'] = $acl;
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $stub                     = new NotificationsMenu($app);
        $guardMock                = m::mock('\Antares\Contracts\Auth\Guard');
        $guardMock->shouldReceive('guest')->andReturn(false);
        $this->assertTrue($stub->authorize($guardMock));
    }

}
