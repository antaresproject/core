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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Http\Filters\Tests;

use Antares\Contracts\Authorization\Authorization;
use Antares\Tester\Http\Filters\CanManage as Stub;
use Antares\Contracts\Authorization\Factory;
use Antares\Contracts\Foundation\Foundation;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Auth\Guard;
use Antares\Testing\TestCase;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;
use Mockery as m;

class CanManageTest extends TestCase
{

    /**
     * testing filter method
     * 
     * @test
     */
    public function testFilter()
    {

        $foundation = m::mock(Foundation::class);
        $auth       = m::mock(Guard::class);
        $config     = m::mock(Repository::class);
        $acl        = m::mock(Authorization::class);
        $factory1   = m::mock(Factory::class);
        $factory1->shouldReceive('make')->andReturnSelf()
                ->shouldReceive('can')->andReturn(false);

        $route   = m::mock(Route::class);
        $request = m::mock(Request::class);

        $foundation->shouldReceive('acl')->once()->andReturn($acl)
                ->shouldReceive('handles')->once()->with('antares::login')->andReturn('http://localhost/admin/login');
        $acl->shouldReceive('can')->once()->with('manage-antares')->andReturn(false);
        $auth->shouldReceive('guest')->once()->andReturn(true);
        $config->shouldReceive('get')->once()->with('antares/foundation::routes.guest')->andReturn('antares::login');

        $stub1 = new Stub($foundation, $factory1);
        $this->assertInstanceOf(RedirectResponse::class, $stub1->filter($route, $request, 'tools-tester'));

        $factory2 = m::mock(Factory::class);
        $factory2->shouldReceive('make')
                ->andReturnSelf()
                ->shouldReceive('can')
                ->andReturn(true);

        $stub2 = new Stub($foundation, $factory2);
        $this->assertNull($stub2->filter($route, $request, 'tools-tester'));
    }

}
