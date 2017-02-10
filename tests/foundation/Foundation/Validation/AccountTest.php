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
 namespace Antares\Foundation\Tests\Validation;

use Mockery as m;
use Antares\Foundation\Validation\Account;

class AccountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Validation\Account.
     *
     * @test
     */
    public function testInstance()
    {
        $events  = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory = m::mock('\Illuminate\Contracts\Validation\Factory');

        $stub = new Account($factory, $events);

        $this->assertInstanceOf('\Antares\Support\Validator', $stub);
    }

    /**
     * Test Antares\Foundation\Validation\Account validation.
     *
     * @test
     */
    public function testValidation()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = [
            'email'    => 'admin@antaresplatform.com',
            'fullname' => 'Administrator',
        ];

        $rules = [
            'email'    => ['required', 'email'],
            'fullname' => ['required'],
        ];

        $factory->shouldReceive('make')->once()->with($input, $rules, [])->andReturn($validator);
        $events->shouldReceive('fire')->once()->with('antares.validate: user.account', m::any())->andReturnNull();

        $stub       = new Account($factory, $events);
        $validation = $stub->with($input);

        $this->assertEquals($validator, $validation);
    }

    /**
     * Test Antares\Foundation\Validation\User on create setting.
     *
     * @test
     */
    public function testValidationOnRegister()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = [
            'email'    => 'admin@antaresplatform.com',
            'fullname' => 'Administrator',
        ];

        $rules = [
            'email'                 => ['required', 'email', 'unique:users,email'],
            'fullname'              => ['required'],
            'password'              => ['sometimes', 'required'],
            'password_confirmation' => ['sometimes', 'same:password'],
        ];

        $factory->shouldReceive('make')->once()->with($input, $rules, [])->andReturn($validator);
        $events->shouldReceive('fire')->once()->with('antares.validate: user.account', m::any())->andReturnNull()
            ->shouldReceive('fire')->once()->with('antares.validate: user.account.register', m::any())->andReturnNull();

        $stub       = new Account($factory, $events);
        $validation = $stub->on('register')->with($input);

        $this->assertEquals($validator, $validation);
    }

    /**
     * Test Antares\Foundation\Validation\Account on change
     * password.
     *
     * @test
     */
    public function testValidationOnChangePassword()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = [
            'current_password'      => '123456',
            'new_password'          => 'qwerty',
            'password_confirmation' => 'qwerty',
        ];

        $rules = [
            'current_password'      => ['required'],
            'new_password'          => ['required', 'different:current_password'],
            'password_confirmation' => ['same:new_password'],
        ];

        $factory->shouldReceive('make')->once()->with($input, $rules, [])->andReturn($validator);

        $stub       = new Account($factory, $events);
        $validation = $stub->on('changePassword')->with($input);

        $this->assertEquals($validator, $validation);
    }
}
