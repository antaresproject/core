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

namespace Antares\Support\TestCase;

use Mockery as m;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $_SERVER['validator.onFoo'] = null;
        $_SERVER['validator.onFoo'] = null;
    }

    /**
     * Test Antares\Support\Validator.
     *
     * @test
     */
    public function testValidation()
    {
        $event     = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $validator = m::mock('\Illuminate\Contracts\Validation\Factory');

        $rules   = ['email' => ['email', 'foo:2'], 'name' => 'any'];
        $phrases = ['email.required' => 'Email required'];

        $event->shouldReceive('fire')->once()->with('foo.event', m::any())->andReturn(null);
        $validator->shouldReceive('make')->once()->with([], $rules, $phrases)
                ->andReturn(m::mock('\Illuminate\Validation\Validator'));

        $stub = new FooValidator($validator, $event);
        $stub->on('foo', ['antares'])->bind(['id' => '2']);

        $validation = $stub->with([], 'foo.event');

        $this->assertEquals('antares', $_SERVER['validator.onFoo']);
        $this->assertEquals($validation, $_SERVER['validator.extendFoo']);
        $this->assertInstanceOf('\Illuminate\Validation\Validator', $validation);
    }

    /**
     * Test Antares\Support\Validator without any scope.
     *
     * @test
     */
    public function testValidationWithoutAScope()
    {
        $event     = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $validator = m::mock('\Illuminate\Contracts\Validation\Factory');

        $rules   = ['email' => ['email', 'foo:2'], 'name' => 'any'];
        $phrases = ['email.required' => 'Email required', 'name' => 'Any name'];

        $validator->shouldReceive('make')->once()->with([], $rules, $phrases)
                ->andReturn(m::mock('\Illuminate\Validation\Validator'));

        $stub = new FooValidator($validator, $event);
        $stub->bind(['id' => '2']);

        $validation = $stub->with([], null, ['name' => 'Any name']);

        $this->assertInstanceOf('\Illuminate\Validation\Validator', $validation);
    }

}

class FooValidator extends \Antares\Support\Validator
{

    protected $rules = [
        'email' => ['email', 'foo:{id}'],
        'name'  => 'any',
    ];
    protected $phrases = [
        'email.required' => 'Email required',
    ];

    protected function onFoo($value)
    {
        $_SERVER['validator.onFoo'] = $value;
    }

    protected function extendFoo($validation)
    {
        $_SERVER['validator.extendFoo'] = $validation;
    }

}
