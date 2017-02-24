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

namespace Antares\Users\Processor\TestCase;

use Antares\Users\Processor\AuthenticateUser;
use Mockery as m;

class AuthenticateUserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Users\Processor\AuthenticateUser::login()
     * method.
     *
     * @test
     */
    public function testLoginMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Auth\Listener\AuthenticateUser');
        $validator = m::mock('\Antares\Users\Validation\AuthenticateUser');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $auth      = m::mock('\Illuminate\Contracts\Auth\Guard');
        $user      = m::mock('\Antares\Model\User, \Illuminate\Contracts\Auth\Authenticatable');

        $input = $this->getInput();

        $validator->shouldReceive('on')->once()->with('login')->andReturn($validator)
                ->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(false);
        $auth->shouldReceive('attempt')->once()->with(m::type('Array'), true)->andReturn(true)
                ->shouldReceive('getUser')->once()->andReturn($user);
        $user->shouldReceive('getAttribute')->once()->with('status')->andReturn(0)
                ->shouldReceive('activate')->once()->andReturnSelf()
                ->shouldReceive('save')->once()->andReturnNull();
        $listener->shouldReceive('userHasLoggedIn')->once()->andReturn('logged.in');

        $stub = new AuthenticateUser($validator, $auth);

        $this->assertEquals('logged.in', $stub->login($listener, $input));
    }

    /**
     * Test Antares\Users\Processor\AuthenticateUser::login()
     * method given failed authentication.
     *
     * @test
     */
    public function testLoginMethodGivenFailedAuthentication()
    {
        $listener  = m::mock('\Antares\Contracts\Auth\Listener\AuthenticateUser');
        $validator = m::mock('\Antares\Users\Validation\AuthenticateUser');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $auth      = m::mock('\Illuminate\Contracts\Auth\Guard');

        $input = $this->getInput();

        $validator->shouldReceive('on')->once()->with('login')->andReturn($validator)
                ->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(false);
        $auth->shouldReceive('attempt')->once()->with(m::type('Array'), true)->andReturn(false);
        $listener->shouldReceive('userLoginHasFailedAuthentication')->once()
                ->with(m::type('Array'))->andReturn('login.authentication.failed');

        $stub = new AuthenticateUser($validator, $auth);

        $this->assertEquals('login.authentication.failed', $stub->login($listener, $input));
    }

    /**
     * Test Antares\Users\Processor\AuthenticateUser::login()
     * method given failed validation.
     *
     * @test
     */
    public function testLoginMethodGivenFailedValidation()
    {
        $listener  = m::mock('\Antares\Contracts\Auth\Listener\AuthenticateUser');
        $validator = m::mock('\Antares\Users\Validation\AuthenticateUser');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $auth      = m::mock('\Illuminate\Contracts\Auth\Guard');

        $input = $this->getInput();

        $validator->shouldReceive('on')->once()->with('login')->andReturn($validator)
                ->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(true)
                ->shouldReceive('getMessageBag')->once()->andReturn([]);
        $listener->shouldReceive('userLoginHasFailedValidation')->once()
                ->with(m::type('Array'))->andReturn('login.validation.failed');

        $stub = new AuthenticateUser($validator, $auth);

        $this->assertEquals('login.validation.failed', $stub->login($listener, $input));
    }

    /**
     * Test Antares\Users\Processor\AuthenticateUser::logout()
     * method.
     *
     * @test
     */
    public function testLogoutMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Auth\Listener\AuthenticateUser');
        $validator = m::mock('\Antares\Users\Validation\AuthenticateUser');
        $auth      = m::mock('\Illuminate\Contracts\Auth\Guard');

        $stub = new AuthenticateUser($validator, $auth);

        $auth->shouldReceive('logout')->once()->andReturnNull();

        $listener->shouldReceive('userHasLoggedOut')->once()->andReturn('logged.out');

        $this->assertEquals('logged.out', $stub->logout($listener));
    }

    /**
     * Get sample input.
     *
     * @return array
     */
    protected function getInput()
    {
        return [
            'email'    => 'hello@antaresplatform.com',
            'password' => '123456',
            'remember' => 'yes',
        ];
    }

}
