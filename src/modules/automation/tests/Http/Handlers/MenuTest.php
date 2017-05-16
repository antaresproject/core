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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Automation\Http\Handlers\TestCase;

use Antares\Automation\Http\Handlers\Menu as AutomationMenu;
use Antares\Foundation\Support\MenuHandler;
use Antares\Testbench\TestCase;
use Mockery as m;

class MenuTest extends TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    public function testItIsInitializable()
    {

        $app  = m::mock('Illuminate\Container\Container');
        $menu = m::mock(Menu::class);
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $stub = new AutomationMenu($app);
        $this->assertInstanceOf(AutomationMenu::class, $stub);
        $this->assertInstanceOf(MenuHandler::class, $stub);
    }

    public function testItShouldBeChildOfExtensionGivenExtensionIsAvailable()
    {
        $app  = m::mock('Illuminate\Container\Container');
        $menu = m::mock(Menu::class);
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $menu->shouldReceive('has')->once()->with('extensions')->andReturn(true);
        $stub = new AutomationMenu($app);
        $this->assertEquals('^:settings', $stub->getPositionAttribute());
    }

    public function testItShouldNextToHomeGivenExtensionIsntAvailable()
    {
        $app  = m::mock('Illuminate\Container\Container');
        $menu = m::mock(Menu::class);
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $menu->shouldReceive('has')->once()->with('extensions')->andReturn(false);
        $stub = new AutomationMenu($app);
        $this->assertEquals('>:home', $stub->getPositionAttribute());
    }

    /**
     * Tests Antares\Automation\Http\Handlers\Menu::authorize
     */
    public function testAuthorize()
    {
        $this->app['antares.platform.memory'] = m::mock('Antares\Memory\Provider');
        $app                                  = m::mock('Illuminate\Container\Container');
        $menu                                 = m::mock(Menu::class);
        $acl                                  = m::mock('Antares\Authorization\Factory')
                ->shouldReceive('make')->with("antares/automation")->andReturnSelf()
                ->shouldReceive("can")->with(m::type("String"))->andReturn(true)
                ->shouldReceive('attach')->with($this->app['antares.platform.memory'])->andReturnSelf()
                ->getMock();

        $this->app['antares.acl'] = $acl;
        $app->shouldReceive('make')->once()->with('antares.platform.menu')->andReturn($menu);
        $stub                     = new AutomationMenu($app);
        $guardMock                = m::mock('\Antares\Contracts\Auth\Guard');
        $guardMock->shouldReceive('guest')->andReturn(false);
        $this->assertTrue($stub->authorize($guardMock));
    }

}
