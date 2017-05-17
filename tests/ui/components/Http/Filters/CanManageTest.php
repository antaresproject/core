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
 * @package    Widgets
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Widgets\Http\Filters\Tests;

use Antares\Contracts\Authorization\Authorization;
use Antares\Contracts\Authorization\Factory;
use Antares\Contracts\Foundation\Foundation;
use Antares\Widgets\Http\Filters\CanManage;
use Illuminate\Http\RedirectResponse;
use Antares\Testing\TestCase;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;
use Mockery as m;

class CanManageTest extends TestCase
{

    /**
     * Test Antares\Widgets\Http\Filters\CanManage::filter() method when can manage.
     *
     * @test
     */
    public function testFilterMethodWhenCanNotManage()
    {
        $foundation = m::mock(Foundation::class);
        $auth       = m::mock(Factory::class);
        $acl        = m::mock(Authorization::class);
        $auth->shouldReceive('make')->with('antares/widgets')->andReturn($acl);


        $route   = m::mock(Route::class);
        $request = m::mock(Request::class);

        $acl->shouldReceive('can')->once()->with('manage widgets')->andReturn(false);
        $auth->shouldReceive('guest')->once()->andReturn(true);


        $stub = new CanManage($foundation, $auth);
        $this->assertInstanceOf(RedirectResponse::class, $stub->filter($route, $request, 'manage-widgets'));
    }

    /**
     * Test Antares\Widgets\Http\Filters\CanManage::filter() method when can manage.
     *
     * @test
     */
    public function testFilterMethodWhenCanManage()
    {
        $foundation = m::mock(Foundation::class);
        $auth       = m::mock(Factory::class);
        $acl        = m::mock(Authorization::class);
        $auth->shouldReceive('make')->with('antares/widgets')->andReturn($acl);


        $route   = m::mock(Route::class);
        $request = m::mock(Request::class);

        $acl->shouldReceive('can')->once()->with('manage widgets')->andReturn(true);
        $auth->shouldReceive('guest')->once()->andReturn(true);


        $stub = new CanManage($foundation, $auth);
        $this->assertNull($stub->filter($route, $request, 'manage-widgets'));
    }

}
