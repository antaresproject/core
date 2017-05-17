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

namespace Antares\Users\Tests\Validation;

use Antares\Testing\ApplicationTestCase;
use Antares\Users\Validation\User;
use Mockery as m;

class UserTest extends ApplicationTestCase
{

    /**
     * Test Antares\Users\Validation\User.
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
     * Test Antares\Users\Validation\User validation.
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

        $factory->shouldReceive('make')->once()->andReturn($validator);

        $events->shouldReceive('fire')->once()->with('antares.validate: users', m::any())->andReturnNull()
                ->shouldReceive('fire')->once()->with('antares.validate: user.account', m::any())->andReturnNull();

        $stub       = new User($factory, $events);
        $validation = $stub->with($input);

        $this->assertEquals($validator, $validation);
    }

    /**
     * Test Antares\Users\Validation\User on create.
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

        $factory->shouldReceive('make')->once()->andReturn($validator);
        $events->shouldReceive('fire')->once()->with('antares.validate: users', m::any())->andReturnNull()
                ->shouldReceive('fire')->once()->with('antares.validate: user.account', m::any())->andReturnNull();

        $stub       = new User($factory, $events);
        $validation = $stub->on('create')->with($input);

        $this->assertEquals($validator, $validation);
    }

}
