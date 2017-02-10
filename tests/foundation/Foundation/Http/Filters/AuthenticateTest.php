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
use Antares\Foundation\Http\Filters\Authenticate;

class AuthenticateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Filters\Authenticated::filter()
     * method when request is ajax.
     *
     * @test
     */
    public function testFilterMethodWhenAjaxRequest()
    {
        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $auth       = m::mock('\Illuminate\Contracts\Auth\Guard');
        $config     = m::mock('\Illuminate\Contracts\Config\Repository');
        $response   = m::mock('\Illuminate\Contracts\Routing\ResponseFactory');

        $route   = m::mock('\Illuminate\Routing\Route');
        $request = m::mock('\Illuminate\Http\Request');

        $auth->shouldReceive('guest')->once()->andReturn(true);
        $request->shouldReceive('ajax')->once()->andReturn(true);
        $response->shouldReceive('make')->once()->with('Unauthorized', 401)->andReturn('foo');

        $stub = new Authenticate($foundation, $auth, $config, $response);
        $this->assertEquals('foo', $stub->filter($route, $request));
    }

    /**
     * Test Antares\Foundation\Filters\Authenticated::filter()
     * method when request is html.
     *
     * @test
     */
    public function testFilterMethodWhenHtmlRequest()
    {
        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $auth       = m::mock('\Illuminate\Contracts\Auth\Guard');
        $config     = m::mock('\Illuminate\Contracts\Config\Repository');
        $response   = m::mock('\Illuminate\Contracts\Routing\ResponseFactory');

        $route   = m::mock('\Illuminate\Routing\Route');
        $request = m::mock('\Illuminate\Http\Request');

        $auth->shouldReceive('guest')->once()->andReturn(true);
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $config->shouldReceive('get')->once()->with('antares/foundation::routes.guest')->andReturn('antares::login');
        $foundation->shouldReceive('handles')->once()->with('antares::login')->andReturn('http://localhost/admin/login');
        $response->shouldReceive('redirectGuest')->once()->with('http://localhost/admin/login')->andReturn('foo');

        $stub = new Authenticate($foundation, $auth, $config, $response);
        $this->assertEquals('foo', $stub->filter($route, $request));
    }

    /**
     * Test Antares\Foundation\Filters\Authenticated::filter()
     * method os not guest.
     *
     * @test
     */
    public function testFilterMethodIsNotGuest()
    {
        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $auth       = m::mock('\Illuminate\Contracts\Auth\Guard');
        $config     = m::mock('\Illuminate\Contracts\Config\Repository');
        $response   = m::mock('\Illuminate\Contracts\Routing\ResponseFactory');

        $route   = m::mock('\Illuminate\Routing\Route');
        $request = m::mock('\Illuminate\Http\Request');

        $auth->shouldReceive('guest')->once()->andReturn(false);

        $stub = new Authenticate($foundation, $auth, $config, $response);
        $this->assertNull($stub->filter($route, $request));
    }
}
