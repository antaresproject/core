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
 namespace Antares\Foundation\Http\Filters\TestCase;

use Mockery as m;
use Antares\Foundation\Http\Filters\CanManage;

class CanManageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Filters\IsInstalled::filter()
     * method when can manage.
     *
     * @test
     */
    public function testFilterMethodWhenCanManage()
    {
        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $auth       = m::mock('\Illuminate\Contracts\Auth\Guard');
        $config     = m::mock('\Illuminate\Contracts\Config\Repository');
        $acl        = m::mock('\Antares\Contracts\Authorization\Authorization');

        $route   = m::mock('\Illuminate\Routing\Route');
        $request = m::mock('\Illuminate\Http\Request');

        $foundation->shouldReceive('acl')->once()->andReturn($acl)
            ->shouldReceive('handles')->once()->with('antares::login')->andReturn('http://localhost/admin/login');
        $acl->shouldReceive('can')->once()->with('manage-antares')->andReturn(false);
        $auth->shouldReceive('guest')->once()->andReturn(true);
        $config->shouldReceive('get')->once()->with('antares/foundation::routes.guest')->andReturn('antares::login');

        $stub = new CanManage($foundation, $auth, $config);

        $this->assertInstanceOf('\Illuminate\Http\RedirectResponse', $stub->filter($route, $request, 'antares'));
    }

    /**
     * Test Antares\Foundation\Filters\IsInstalled::filter()
     * method when can't manage.
     *
     * @test
     */
    public function testFilterMethodWhenCantManage()
    {
        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $auth       = m::mock('\Illuminate\Contracts\Auth\Guard');
        $config     = m::mock('\Illuminate\Contracts\Config\Repository');
        $acl        = m::mock('\Antares\Contracts\Authorization\Authorization');

        $route   = m::mock('\Illuminate\Routing\Route');
        $request = m::mock('\Illuminate\Http\Request');

        $foundation->shouldReceive('acl')->once()->andReturn($acl);
        $acl->shouldReceive('can')->once()->with('manage-foo')->andReturn(true);

        $stub = new CanManage($foundation, $auth, $config);

        $this->assertNull($stub->filter($route, $request, 'foo'));
    }
}
