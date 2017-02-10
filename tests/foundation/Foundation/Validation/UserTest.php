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
use Antares\Foundation\Validation\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Validation\User.
     *
     * @test
     */
    public function testInstance()
    {
        $events  = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory = m::mock('\Illuminate\Contracts\Validation\Factory');

        $stub = new User($factory, $events);

        $this->assertInstanceOf('\Antares\Support\Validator', $stub);
    }

    /**
     * Test Antares\Foundation\Validation\User validation.
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
            'roles'    => 1,
        ];

        $rules = [
            'email'    => ['required', 'email'],
            'fullname' => ['required'],
            'roles'    => ['required'],
        ];

        $factory->shouldReceive('make')->once()->with($input, $rules, [])->andReturn($validator);

        $events->shouldReceive('fire')->once()->with('antares.validate: users', m::any())->andReturnNull()
            ->shouldReceive('fire')->once()->with('antares.validate: user.account', m::any())->andReturnNull();

        $stub       = new User($factory, $events);
        $validation = $stub->with($input);

        $this->assertEquals($validator, $validation);
    }

    /**
     * Test Antares\Foundation\Validation\User on create.
     *
     * @test
     */
    public function testValidationOnCreate()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = [
            'email'    => 'admin@antaresplatform.com',
            'fullname' => 'Administrator',
            'roles'    => 1,
            'password' => '123456',
        ];

        $rules = [
            'email'    => ['required', 'email'],
            'fullname' => ['required'],
            'roles'    => ['required'],
            'password' => ['required'],
        ];

        $factory->shouldReceive('make')->once()->with($input, $rules, [])->andReturn($validator);
        $events->shouldReceive('fire')->once()->with('antares.validate: users', m::any())->andReturnNull()
            ->shouldReceive('fire')->once()->with('antares.validate: user.account', m::any())->andReturnNull();

        $stub       = new User($factory, $events);
        $validation = $stub->on('create')->with($input);

        $this->assertEquals($validator, $validation);
    }
}
