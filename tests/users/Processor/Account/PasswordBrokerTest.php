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

namespace Antares\Users\Processor\Account\TestCase;

use Illuminate\Contracts\Auth\PasswordBroker as Password;
use Antares\Users\Processor\Account\PasswordBroker;
use Antares\Support\Facades\Foundation;
use Illuminate\Support\Facades\Auth;
use Antares\Testing\TestCase;
use Mockery as m;

class PasswordBrokerTest extends TestCase
{

    /**
     * Test Antares\Users\Processor\Account\PasswordBroker::store()
     * method.
     *
     * @test
     */
    public function testStoreMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Auth\Listener\PasswordResetLink');
        $validator = m::mock('\Antares\Users\Validation\AuthenticateUser');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $password  = m::mock('\Illuminate\Contracts\Auth\PasswordBroker');
        $memory    = m::mock('\Antares\Contracts\Memory\Provider');
        $message   = m::mock('\Illuminate\Mailer\Message');

        $input = $this->getStoreInput();

        $stub = new PasswordBroker($validator, $password);

        $validator->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(false);
        $memory->shouldReceive('get')->once()->with('site.name', 'Antares')->andReturn('Antares');
        $message->shouldReceive('subject')->once()->with(m::type('String'))->andReturnNull();
        $password->shouldReceive('sendResetLink')->once()
                ->with(['email' => $input['email']], m::type('Closure'))
                ->andReturnUsing(function ($d, $c) use ($message) {
                    $c($message);

                    return Password::RESET_LINK_SENT;
                });
        $listener->shouldReceive('resetLinkSent')->once()->with(Password::RESET_LINK_SENT)->andReturn('reset.sent');

        Foundation::shouldReceive('memory')->once()->andReturn($memory);

        $this->assertEquals('reset.sent', $stub->store($listener, $input));
    }

    /**
     * Test Antares\Users\Processor\Account\PasswordBroker::store()
     * method given invalid user.
     *
     * @test
     */
    public function testStoreMethodGivenInvalidUser()
    {
        $listener  = m::mock('\Antares\Contracts\Auth\Listener\PasswordResetLink');
        $validator = m::mock('\Antares\Users\Validation\AuthenticateUser');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $password  = m::mock('\Illuminate\Contracts\Auth\PasswordBroker');
        $memory    = m::mock('\Antares\Contracts\Memory\Provider');

        $input = $this->getStoreInput();

        $stub = new PasswordBroker($validator, $password);

        $validator->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(false);
        $memory->shouldReceive('get')->once()->with('site.name', 'Antares')->andReturn('Antares');
        $password->shouldReceive('sendResetLink')->once()
                ->with(['email' => $input['email']], m::type('Closure'))
                ->andReturnUsing(function ($d, $c) {
                    return Password::INVALID_USER;
                });
        $listener->shouldReceive('resetLinkFailed')->once()->with(Password::INVALID_USER)->andReturn('reset.not.sent');

        Foundation::shouldReceive('memory')->once()->andReturn($memory);

        $this->assertEquals('reset.not.sent', $stub->store($listener, $input));
    }

    /**
     * Test Antares\Users\Processor\Account\PasswordBroker::store()
     * method given failed validation.
     *
     * @test
     */
    public function testStoreMethodGivenFailedValidation()
    {
        $listener  = m::mock('\Antares\Contracts\Auth\Listener\PasswordResetLink');
        $validator = m::mock('\Antares\Users\Validation\AuthenticateUser');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $password  = m::mock('\Illuminate\Contracts\Auth\PasswordBroker');

        $input = $this->getStoreInput();

        $stub = new PasswordBroker($validator, $password);

        $validator->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(true)
                ->shouldReceive('getMessageBag')->once()->andReturn([]);
        $listener->shouldReceive('resetLinkFailedValidation')->once()->with([])->andReturn('reset.failed.validation');

        $this->assertEquals('reset.failed.validation', $stub->store($listener, $input));
    }

    /**
     * Test Antares\Users\Processor\Account\PasswordBroker::update()
     * method.
     *
     * @test
     */
    public function testUpdateMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Auth\Listener\PasswordReset');
        $validator = m::mock('\Antares\Users\Validation\AuthenticateUser');
        $password  = m::mock('\Illuminate\Contracts\Auth\PasswordBroker');
        $user      = m::mock('\Antares\Model\User');

        $input = $this->getUpdateInput();

        $stub = new PasswordBroker($validator, $password);

        $user->shouldReceive('setAttribute')->once()->with('password', $input['password'])->andReturnNull()
                ->shouldReceive('save')->once()->andReturnNull();
        $password->shouldReceive('reset')->once()
                ->with($input, m::type('Closure'))
                ->andReturnUsing(function ($d, $c) use ($user, $input) {
                    $c($user, $input['password']);

                    return Password::PASSWORD_RESET;
                });
        $listener->shouldReceive('passwordHasReset')->once()->with(Password::PASSWORD_RESET)->andReturn('reset.done');

        Auth::shouldReceive('login')->once()->with($user)->andReturnNull();

        $this->assertEquals('reset.done', $stub->update($listener, $input));
    }

    /**
     * Test Antares\Users\Processor\Account\PasswordBroker::store()
     * method given failed execution.
     *
     * @test
     */
    public function testUpdateMethodGivenFailed()
    {
        $listener  = m::mock('\Antares\Contracts\Auth\Listener\PasswordReset');
        $validator = m::mock('\Antares\Users\Validation\AuthenticateUser');
        $password  = m::mock('\Illuminate\Contracts\Auth\PasswordBroker');

        $input = $this->getUpdateInput();

        $stub = new PasswordBroker($validator, $password);

        $password->shouldReceive('reset')->once()
                ->with($input, m::type('Closure'))
                ->andReturnUsing(function ($d, $c) {
                    return Password::INVALID_PASSWORD;
                });
        $listener->shouldReceive('passwordResetHasFailed')->once()->with(Password::INVALID_PASSWORD)->andReturn('reset.failed');

        $this->assertEquals('reset.failed', $stub->update($listener, $input));
    }

    /**
     * Get request input for store.
     *
     * @return array
     */
    protected function getStoreInput()
    {
        return [
            'email' => 'email@antaresplatform.com',
        ];
    }

    /**
     * Get request input for update.
     *
     * @return array
     */
    protected function getUpdateInput()
    {
        return [
            'email'                 => 'email@antaresplatform.com',
            'password'              => '123456',
            'password_confirmation' => '123456',
            'token'                 => 'auniquetoken',
        ];
    }

}
