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

namespace Antares\Foundation\Http\Handlers\TestCase;

use Antares\Testing\ApplicationTestCase;
use Antares\Foundation\Http\Handlers\SettingsMenu as SettingMenuHandler;
use Illuminate\Container\Container;
use Mockery as m;

class SettingsMenuTest extends ApplicationTestCase
{

    /**
     * Test Antares\Foundation\Http\Handlers\SettingMenuHandler::handle()
     * method with authorized user.
     *
     * @test
     */
    public function testCreatingMenuWithAuthorizedUser()
    {
        $stub = new SettingMenuHandler($this->app);
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
        $app                                                  = new Container();
        $app['antares.platform.menu']                         = $menu                                                 = m::mock(\Antares\UI\TemplateBase\Menu::class);
        $app['Antares\Contracts\Authorization\Authorization'] = $acl                                                  = m::mock('\Antares\Contracts\Authorization\Authorization');

        $acl->shouldReceive('can')->with('manage-antares')->once()->andReturn(false);

        $stub = new SettingMenuHandler($app);
        $this->assertNull($stub->handle());
    }

}
