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
use Antares\Foundation\Http\Filters\VerifyCsrfToken;

class VerifyCsrfTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Filters\CanBeInstalled::filter()
     * method with invalid csrf token.
     *
     * @expectedException \Illuminate\Session\TokenMismatchException
     */
    public function testFilterMethodWithInvalidToken()
    {
        $encrypter = m::mock('\Illuminate\Contracts\Encryption\Encrypter');
        $session   = m::mock('\Illuminate\Session\SessionInterface');
        $route     = m::mock('\Illuminate\Routing\Route');
        $request   = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('session')->once()->andReturn($session)
            ->shouldReceive('input')->once()->with('_token')->andReturn('b');
        $session->shouldReceive('token')->once()->andReturn('a');

        $stub = new VerifyCsrfToken($encrypter);

        $stub->filter($route, $request);
    }

    /**
     * Test Antares\Foundation\Filters\CanBeInstalled::filter()
     * method with valid csrf token.
     *
     * @test
     */
    public function testFilterMethodWithValidToken()
    {
        $encrypter = m::mock('\Illuminate\Contracts\Encryption\Encrypter');
        $session   = m::mock('\Illuminate\Session\SessionInterface');
        $route     = m::mock('\Illuminate\Routing\Route');
        $request   = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('session')->once()->andReturn($session)
            ->shouldReceive('input')->once()->with('_token')->andReturn('a');
        $session->shouldReceive('token')->once()->andReturn('a');

        $stub = new VerifyCsrfToken($encrypter);

        $this->assertNull($stub->filter($route, $request));
    }

    /**
     * Test Antares\Foundation\Filters\CanBeInstalled::filter()
     * method with valid csrf token using header.
     *
     * @test
     */
    public function testFilterMethodWithValidTokenUsingHeaders()
    {
        $encrypter = m::mock('\Illuminate\Contracts\Encryption\Encrypter');
        $session   = m::mock('\Illuminate\Session\SessionInterface');
        $route     = m::mock('\Illuminate\Routing\Route');
        $request   = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('session')->once()->andReturn($session)
            ->shouldReceive('header')->once()->with('X-CSRF-TOKEN')->andReturn('a')
            ->shouldReceive('input')->once()->with('_token')->andReturnNull();
        $session->shouldReceive('token')->once()->andReturn('a');

        $stub = new VerifyCsrfToken($encrypter);

        $this->assertNull($stub->filter($route, $request));
    }

    /**
     * Test Antares\Foundation\Filters\CanBeInstalled::filter()
     * method with valid csrf token using encrypted header.
     *
     * @test
     */
    public function testFilterMethodWithValidTokenUsingEncryptedHeaders()
    {
        $encrypter = m::mock('\Illuminate\Contracts\Encryption\Encrypter');
        $session   = m::mock('\Illuminate\Session\SessionInterface');
        $route     = m::mock('\Illuminate\Routing\Route');
        $request   = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('session')->once()->andReturn($session)
            ->shouldReceive('header')->once()->with('X-XSRF-TOKEN')->andReturn('foobar')
            ->shouldReceive('header')->once()->with('X-CSRF-TOKEN')->andReturnNull()
            ->shouldReceive('input')->once()->with('_token')->andReturnNull();
        $encrypter->shouldReceive('decrypt')->once()->with('foobar')->andReturn('a');
        $session->shouldReceive('token')->once()->andReturn('a');

        $stub = new VerifyCsrfToken($encrypter);

        $this->assertNull($stub->filter($route, $request));
    }
}
