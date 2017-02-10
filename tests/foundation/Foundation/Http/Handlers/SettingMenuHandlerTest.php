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
 namespace Antares\Foundation\Http\Handlers\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Foundation\Http\Handlers\SettingMenuHandler;

class SettingMenuHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Antares\Foundation\Http\Handlers\SettingMenuHandler::handle()
     * method with authorized user.
     *
     * @test
     */
    public function testCreatingMenuWithAuthorizedUser()
    {
        $app = new Container();
        $app['antares.app'] = $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $app['antares.platform.menu'] = $menu = m::mock('\Antares\Widget\Handlers\Menu');
        $app['translator'] = $translator = m::mock('\Illuminate\Translator\Translator');
        $app['Antares\Contracts\Authorization\Authorization'] = $acl = m::mock('\Antares\Contracts\Authorization\Authorization');

        $acl->shouldReceive('can')->with('manage-antares')->once()->andReturn(true);
        $translator->shouldReceive('trans')->once()->with('antares/foundation::title.settings.list')->andReturn('settings');
        $foundation->shouldReceive('handles')->once()->with('antares::settings')->andReturn('admin/settings');
        $menu->shouldReceive('add')->once()->andReturnSelf()
            ->shouldReceive('title')->once()->with('settings')->andReturnSelf()
            ->shouldReceive('link')->once()->with('admin/settings')->andReturnNull();

        $stub = new SettingMenuHandler($app);
        $this->assertNull($stub->handle());
    }

    /**
     * Test Antares\Foundation\Http\Handlers\SettingMenuHandler::handle()
     * method without authorized user.
     *
     * @test
     */
    public function testCreatingMenuWithoutAuthorizedUser()
    {
        $app = new Container();
        $app['antares.platform.menu'] = $menu = m::mock('\Antares\Widget\Handlers\Menu');
        $app['Antares\Contracts\Authorization\Authorization'] = $acl = m::mock('\Antares\Contracts\Authorization\Authorization');

        $acl->shouldReceive('can')->with('manage-antares')->once()->andReturn(false);

        $stub = new SettingMenuHandler($app);
        $this->assertNull($stub->handle());
    }
}
