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

namespace Antares\Users\Processor\TestCase;

use Antares\Users\Processor\AuthenticateUser;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class AuthenticateUserTest extends ApplicationTestCase
{

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


        $input = $this->getInput();

        $validator->shouldReceive('on')->once()->with('login')->andReturn($validator)->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(false);

        $listener->shouldReceive('userHasLoggedIn')->once()->andReturn('login.success');

        $acl = m::mock('\Antares\Authorization\Factory');
        $acl->shouldReceive('make')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('can')->withAnyArgs()->andReturn(true);

        $user = \Antares\Model\User::query()->findOrFail(1);


        $guard = m::mock('Antares\Auth\SessionGuard');
        $guard->shouldReceive('attempt')->andReturn(true)
                ->shouldReceive('getUser')->andReturn($user);
        $stub  = new AuthenticateUser($guard, $validator, $acl);

        $this->assertEquals('login.success', $stub->login($listener, $input));
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


        $validator->shouldReceive('on')->once()->with('login')->andReturn($validator)
                ->shouldReceive('with')->once()->with([
            'email' => 'foo@bar.com'
        ])->andReturn($resolver);

        $resolver->shouldReceive('fails')->once()->andReturn(false);

        $listener->shouldReceive('userIsNotActive')->once()->andReturn('login.authentication.failed');
        $acl = m::mock('\Antares\Authorization\Factory');
        $acl->shouldReceive('make')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('can')->withAnyArgs()->andReturn(true);

        $guard = m::mock('Antares\Auth\SessionGuard');
        $guard->shouldReceive('attempt')->andReturn(true)
                ->shouldReceive('getUser')->andReturn(new \Antares\Model\User())
                ->shouldReceive('logout')->andReturn(false);

        $stub = new AuthenticateUser($guard, $validator, $acl);
        $this->assertEquals('login.authentication.failed', $stub->login($listener, [
                    'email' => 'foo@bar.com'
        ]));
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


        $validator->shouldReceive('on')->once()->with('login')->andReturn($validator)
                ->shouldReceive('with')->once()->with([
            'email' => 'foo@bar.com'
        ])->andReturn($resolver);

        $resolver->shouldReceive('fails')->once()->andReturn(true)
                ->shouldReceive('getMessageBag')->once()->andReturn($messageBag = m::mock(\Antares\Messages\MessageBag::class));

        $listener->shouldReceive('userLoginHasFailedValidation')->once()->andReturn('login.validation.failed');
        $acl = m::mock('\Antares\Authorization\Factory');
        $acl->shouldReceive('make')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('can')->withAnyArgs()->andReturn(true);

        $guard = m::mock('Antares\Auth\SessionGuard');
        $guard->shouldReceive('attempt')->andReturn(true)
                ->shouldReceive('getUser')->andReturn(new \Antares\Model\User())
                ->shouldReceive('logout')->andReturn(false);

        $stub = new AuthenticateUser($guard, $validator, $acl);
        $this->assertEquals('login.validation.failed', $stub->login($listener, [
                    'email' => 'foo@bar.com'
        ]));
    }

    /**
     * Get sample input.
     *
     * @return array
     */
    protected function getInput()
    {
        return [
            'email'    => 'lukasz.cirut@gmail.com',
            'password' => 'myszka',
            'remember' => 'yes',
        ];
    }

}
