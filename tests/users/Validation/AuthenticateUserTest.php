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

namespace Antares\Users\Validation\TestCase;

use Antares\Users\Validation\AuthenticateUser;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class AuthenticateUserTest extends ApplicationTestCase
{

    /**
     * Test Antares\Users\Validation\Auth.
     *
     * @test
     */
    public function testInstance()
    {
        $events  = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory = m::mock('\Illuminate\Contracts\Validation\Factory');

        $stub = new AuthenticateUser($factory, $events);

        $this->assertInstanceOf('\Antares\Support\Validator', $stub);
    }

    /**
     * Test Antares\Users\Validation\Auth validation.
     *
     * @test
     */
    public function testValidation()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = ['email' => 'admin@antaresplatform.com', 'password' => '123'];
        $rules = ['email' => ['required', 'email']];

        $factory->shouldReceive('make')->once()->andReturn($validator);

        $stub       = new AuthenticateUser($factory, $events);
        $validation = $stub->with($input);

        $this->assertEquals($validator, $validation);
    }

    /**
     * Test Antares\Users\Validation\Auth on login.
     *
     * @test
     */
    public function testValidationOnLogin()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = ['email' => 'admin@antaresplatform.com', 'password' => '123'];
        $rules = ['email' => ['required', 'email'], 'password' => ['required']];

        $factory->shouldReceive('make')->once()->andReturn($validator);

        $stub       = new AuthenticateUser($factory, $events);
        $validation = $stub->on('login')->with($input);

        $this->assertEquals($validator, $validation);
    }

}
