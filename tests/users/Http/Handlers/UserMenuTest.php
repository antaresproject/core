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

namespace Antares\Users\Http\Handlers\TestCase;

use Antares\Testing\ApplicationTestCase;
use Antares\Users\Http\Handlers\UserMenu as UserMenuHandler;
use Illuminate\Container\Container;
use Mockery as m;

class UserMenuHandlerTest extends ApplicationTestCase
{

    /**
     * Test Antares\Users\Http\Handlers\UserMenuHandler::handle()
     * method with authorized user.
     *
     * @test
     */
    public function testCreatingMenuWithAuthorizedUser()
    {

        $app                                                  = new Container();
        $app['antares.app']                                   = $foundation                                           = m::mock('\Antares\Contracts\Foundation\Foundation');
        $app['antares.platform.menu']                         = $menu                                                 = m::mock(\Illuminate\Support\Fluent::class);
        $app['translator']                                    = $translator                                           = m::mock('\Illuminate\Translator\Translator');
        $app['Antares\Contracts\Authorization\Authorization'] = $acl                                                  = m::mock('\Antares\Contracts\Authorization\Authorization');

        $acl->shouldReceive('can')->with(m::type('String'))->once()->andReturn(true);
        $translator->shouldReceive('trans')->once()->with('antares/foundation::title.users.list')->andReturn('users');
        $foundation->shouldReceive('handles')->once()->with(m::type('String'))->andReturn('antares/users');
        $menu->shouldReceive('add')->once()->andReturnSelf()
                ->shouldReceive('title')->once()->with('users')->andReturnSelf()
                ->shouldReceive('link')->once()->with('antares/users')->andReturnSelf()
                ->shouldReceive('icon')->once()->with(m::type('String'))->andReturnSelf()
                ->shouldReceive('entity')->once()->andReturn(new \Illuminate\Support\Fluent());

        $stub = new UserMenuHandler($app);
        $this->assertNull($stub->handle());
    }

    /**
     * Test Antares\Users\Http\Handlers\UserMenuHandler::handle()
     * method with authorized user.
     *
     * @test
     */
    public function testCreatingMenuWithoutAuthorizedUser()
    {
        $app                                                  = new Container();
        $app['antares.platform.menu']                         = $menu                                                 = m::mock(\Antares\UI\TemplateBase\Menu::class);
        $app['Antares\Contracts\Authorization\Authorization'] = $acl                                                  = m::mock('\Antares\Contracts\Authorization\Authorization');

        $acl->shouldReceive('can')->with("users-list")->once()->andReturn(false);

        $stub = new UserMenuHandler($app);
        $this->assertNull($stub->handle());
    }

}
