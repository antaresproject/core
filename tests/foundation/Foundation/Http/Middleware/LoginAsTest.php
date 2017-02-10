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
 namespace Antares\Foundation\Http\Middleware\TestCase;

use Mockery as m;
use Antares\Foundation\Http\Middleware\LoginAs;

class LoginAsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function teardown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Middleware\LoginAs::handle()
     * method without redirection.
     *
     * @test
     */
    public function testHandleMethodWithoutRedirect()
    {
        $acl     = m::mock('\Antares\Contracts\Authorization\Authorization');
        $auth    = m::mock('\Antares\Contracts\Auth\Guard');
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('input')->once()->with('_as')->andReturnNull();
        $acl->shouldReceive('can')->once()->with('manage antares')->andReturn(false);

        $next = function ($request) {
            return 'foo';
        };

        $stub = new LoginAs($acl, $auth);

        $this->assertEquals('foo', $stub->handle($request, $next));
    }

    /**
     * Test Antares\Foundation\Middleware\LoginAs::handle()
     * method with redirection.
     *
     * @test
     */
    public function testHandleMethodWithRedirect()
    {
        $acl     = m::mock('\Antares\Contracts\Authorization\Authorization');
        $auth    = m::mock('\Antares\Contracts\Auth\Guard');
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('input')->once()->with('_as')->andReturn(5)
            ->shouldReceive('url')->once()->andReturn('http://localhost');
        $acl->shouldReceive('can')->once()->with('manage antares')->andReturn(true);
        $auth->shouldReceive('loginUsingId')->once()->with(5)->andReturnNull();

        $next = function ($request) {
            return 'foo';
        };

        $stub = new LoginAs($acl, $auth);

        $this->assertInstanceOf('\Illuminate\Http\RedirectResponse', $stub->handle($request, $next));
    }
}
