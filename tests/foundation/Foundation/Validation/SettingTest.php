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

namespace Antares\Foundation\Tests\Validation;

use Antares\Testing\ApplicationTestCase;
use Mockery as m;
use Antares\Foundation\Validation\Setting;

class SettingTest extends ApplicationTestCase
{

    /**
     * Test Antares\Foundation\Validation\Setting.
     *
     * @test
     */
    public function testInstance()
    {
        $events  = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory = m::mock('\Illuminate\Contracts\Validation\Factory');

        $stub = new Setting($factory, $events);

        $this->assertInstanceOf('\Antares\Support\Validator', $stub);
    }

    /**
     * Test Antares\Foundation\Validation\Setting validation.
     *
     * @test
     */
    public function testValidation()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = [
            'site_name'     => 'Antares Platform',
            'email_address' => 'admin@antaresplatform.com',
            'email_driver'  => 'mail',
            'email_port'    => 25,
        ];

        $rules = [
            'site_name'     => ['required'],
            'email_address' => ['required', 'email'],
            'email_driver'  => ['required', 'in:mail,smtp,sendmail,ses,mailgun,mandrill'],
            'email_port'    => ['numeric'],
        ];

        $factory->shouldReceive('make')->once()->withAnyArgs()->andReturn($validator);
        $events->shouldReceive('fire')->once()->with('antares.validate: settings', m::any())->andReturnNull();

        $stub       = new Setting($factory, $events);
        $validation = $stub->with($input);

        $this->assertEquals($validator, $validation);
    }

    /**
     * Test Antares\Foundation\Validation\Setting on stmp
     * setting.
     *
     * @test
     */
    public function testValidationOnSmtp()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = [
            'site_name'      => 'Antares Platform',
            'email_address'  => 'admin@antaresplatform.com',
            'email_driver'   => 'smtp',
            'email_port'     => 25,
            'email_username' => 'admin@antaresplatform.com',
            'email_password' => '123456',
        ];

        $rules = [
            'site_name'      => ['required'],
            'email_address'  => ['required', 'email'],
            'email_driver'   => ['required', 'in:mail,smtp,sendmail,ses,mailgun,mandrill'],
            'email_port'     => ['numeric'],
            'email_username' => ['required'],
            'email_host'     => ['required'],
        ];

        $factory->shouldReceive('make')->once()->withAnyArgs()->andReturn($validator);
        $events->shouldReceive('fire')->once()->with('antares.validate: settings', m::any())->andReturnNull();


        $stub       = new Setting($factory, $events);
        $validation = $stub->on('smtp')->with($input);

        $this->assertEquals($validator, $validation);
    }

    /**
     * Test Antares\Foundation\Validation\Setting on sendmail
     * setting.
     *
     * @test
     */
    public function testValidationOnSendmail()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = [
            'site_name'      => 'Antares Platform',
            'email_address'  => 'admin@antaresplatform.com',
            'email_driver'   => 'sendmail',
            'email_port'     => 25,
            'email_sendmail' => '/usr/bin/sendmail -t',
        ];

        $rules = [
            'site_name'      => ['required'],
            'email_address'  => ['required', 'email'],
            'email_driver'   => ['required', 'in:mail,smtp,sendmail,ses,mailgun,mandrill'],
            'email_port'     => ['numeric'],
            'email_sendmail' => ['required'],
        ];

        $factory->shouldReceive('make')->once()->withAnyArgs()->andReturn($validator);
        $events->shouldReceive('fire')->once()->with('antares.validate: settings', m::any())->andReturnNull();

        $stub       = new Setting($factory, $events);
        $validation = $stub->on('sendmail')->with($input);

        $this->assertEquals($validator, $validation);
    }

    /**
     * Test Antares\Foundation\Validation\Setting on mailgun
     * setting.
     *
     * @test
     */
    public function testValidationOnMailgun()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = [
            'site_name'     => 'Antares Platform',
            'email_address' => 'admin@antaresplatform.com',
            'email_driver'  => 'mailgun',
            'email_port'    => 25,
            'email_secret'  => 'auniquetoken',
            'email_domain'  => 'antaresplatform.com',
        ];

        $rules = [
            'site_name'     => ['required'],
            'email_address' => ['required', 'email'],
            'email_driver'  => ['required', 'in:mail,smtp,sendmail,ses,mailgun,mandrill'],
            'email_port'    => ['numeric'],
            'email_domain'  => ['required'],
        ];

        $factory->shouldReceive('make')->once()->withAnyArgs()->andReturn($validator);
        $events->shouldReceive('fire')->once()->with('antares.validate: settings', m::any())->andReturnNull();


        $stub       = new Setting($factory, $events);
        $validation = $stub->on('mailgun')->with($input);

        $this->assertEquals($validator, $validation);
    }

    /**
     * Test Antares\Foundation\Validation\Setting on mandrill
     * setting.
     *
     * @test
     */
    public function testValidationOnMandrill()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = [
            'site_name'     => 'Antares Platform',
            'email_address' => 'admin@antaresplatform.com',
            'email_driver'  => 'mandrill',
            'email_port'    => 25,
            'email_secret'  => 'auniquetoken',
        ];

        $rules = [
            'site_name'     => ['required'],
            'email_address' => ['required', 'email'],
            'email_driver'  => ['required', 'in:mail,smtp,sendmail,ses,mailgun,mandrill'],
            'email_port'    => ['numeric'],
        ];

        $factory->shouldReceive('make')->once()->withAnyArgs()->andReturn($validator);
        $events->shouldReceive('fire')->once()->with('antares.validate: settings', m::any())->andReturnNull();


        $stub       = new Setting($factory, $events);
        $validation = $stub->on('mandrill')->with($input);

        $this->assertEquals($validator, $validation);
    }

    /**
     * Test Antares\Foundation\Validation\Setting on SES
     * setting.
     *
     * @test
     */
    public function testValidationOnSes()
    {
        $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory   = m::mock('\Illuminate\Contracts\Validation\Factory');
        $validator = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = [
            'site_name'     => 'Antares Platform',
            'email_address' => 'admin@antaresplatform.com',
            'email_driver'  => 'ses',
            'email_port'    => 25,
            'email_key'     => 'auniquekey',
            'email_secret'  => 'auniquetoken',
            'email_region'  => 'us-east-1',
        ];

        $rules = [
            'site_name'     => ['required'],
            'email_address' => ['required', 'email'],
            'email_driver'  => ['required', 'in:mail,smtp,sendmail,ses,mailgun,mandrill'],
            'email_port'    => ['numeric'],
            'email_key'     => ['required'],
            'email_region'  => ['required', 'in:us-east-1,us-west-2,eu-west-1'],
        ];

        $factory->shouldReceive('make')->once()->withAnyArgs()->andReturn($validator);
        $events->shouldReceive('fire')->once()->with('antares.validate: settings', m::any())->andReturnNull();


        $stub       = new Setting($factory, $events);
        $validation = $stub->on('ses')->with($input);

        $this->assertEquals($validator, $validation);
    }

}
