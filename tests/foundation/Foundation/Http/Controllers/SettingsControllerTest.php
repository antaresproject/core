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

namespace Antares\Foundation\Http\Controllers\TestCase;

use Antares\Testing\ApplicationTestCase;
use Mockery as m;
use Antares\Testing\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Antares\Support\Facades\Messages;
use Antares\Support\Facades\Foundation;

class SettingsControllerTest extends ApplicationTestCase
{

    use \Illuminate\Foundation\Testing\WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $this->disableMiddlewareForAllTests();
    }

    /**
     * Bind dependencies.
     *
     * @return array
     */
    protected function bindDependencies()
    {
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Setting');
        $presenter->shouldReceive('form')->andReturn($form      = m::mock(\Antares\Html\Form\FormBuilder::class));
        $form->shouldReceive('isValid')->once()->withNoArgs()->andReturn(true);
        $validator = m::mock('\Antares\Foundation\Validation\Setting');

        App::instance('Antares\Foundation\Http\Presenters\Setting', $presenter);
        App::instance('Antares\Foundation\Validation\Setting', $validator);

        return [$presenter, $validator];
    }

    /**
     * Test GET /admin/settings.
     *
     * @test
     */
    public function testGetIndexAction()
    {
        $memory = m::mock('\Antares\Contracts\Memory\Provider');
        list($presenter, ) = $this->bindDependencies();

        $memory->shouldReceive('get')->times(16)->andReturn('');
        $presenter->shouldReceive('form')->once()->andReturn('edit.settings');

        $this->app->instance('Antares\Contracts\Memory\Provider', $memory);

        View::shouldReceive('make')->once()->with('antares/foundation::settings.index', m::type('Array'), [])->andReturn('foo');
        $this->call('GET', 'antares/settings/index');
        $this->assertResponseOk();
    }

    /**
     * Test POST /admin/settings.
     *
     * @test
     */
    public function testPostIndexAction()
    {
        $input = [
            'site_name'              => 'Antares',
            'site_description'       => '',
            'site_registrable'       => 'yes',
            'email_driver'           => 'smtp',
            'email_address'          => 'email@antaresplatform.com',
            'email_host'             => 'antaresplatform.com',
            'email_port'             => 25,
            'email_username'         => 'email@antaresplatform.com',
            'email_password'         => '',
            'email_encryption'       => 'ssl',
            'email_sendmail'         => '/usr/bin/sendmail -t',
            'email_secret'           => '',
            'email_queue'            => 'no',
            'enable_change_password' => 'no',
            'enable_change_secret'   => 'no',
        ];

        $memory = m::mock('\Antares\Contracts\Memory\Provider');
        list(, $validator) = $this->bindDependencies();

        $memory->shouldReceive('put')->times(16)->andReturnNull()
                ->shouldReceive('get')->once()->with('email.password')->andReturn('foo')
                ->shouldReceive('get')->once()->with('email.secret')->andReturn('foo');
        $validator->shouldReceive('on')->once()->with('smtp')->andReturn($validator)
                ->shouldReceive('with')->once()->with($input)->andReturn($validator)
                ->shouldReceive('fails')->once()->andReturn(false);

        $this->app->instance('Antares\Contracts\Memory\Provider', $memory);

        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();

        $this->call('POST', 'antares/settings/index', $input);
        $this->assertRedirectedTo('antares/settings/index');
    }

    /**
     * Test POST /admin/settings with validation error.
     *
     * @test
     */
    public function testPostIndexActionGivenValidationError()
    {
        $input = [
            'site_name'              => 'Antares',
            'site_description'       => '',
            'site_registrable'       => 'yes',
            'email_driver'           => 'smtp',
            'email_address'          => 'email@antaresplatform.com',
            'email_host'             => 'antaresplatform.com',
            'email_port'             => 25,
            'email_username'         => 'email@antaresplatform.com',
            'email_password'         => '',
            'email_encryption'       => 'ssl',
            'email_sendmail'         => '/usr/bin/sendmail -t',
            'email_secret'           => '',
            'email_queue'            => 'no',
            'enable_change_password' => 'no',
            'enable_change_secret'   => 'no',
        ];

        list(, $validator) = $this->bindDependencies();

        $presenter  = m::mock('\Antares\Foundation\Http\Presenters\Setting');
        $presenter->shouldReceive('form')->andReturn($form       = m::mock(\Antares\Html\Form\FormBuilder::class));
        $form->shouldReceive('isValid')->once()->withNoArgs()->andReturn(false)
                ->shouldReceive('getMessageBag')->once()->withNoArgs()->andReturn($messageBag = m::mock(\Illuminate\Support\MessageBag::class));

        $messageBag->shouldReceive('getMessageBag')->andReturn($viewErrorBag = m::mock(\Illuminate\Contracts\Support\MessageBag::class));
        App::instance('Antares\Foundation\Http\Presenters\Setting', $presenter);

        $validator->shouldReceive('on')->once()->with('smtp')->andReturn($validator)
                ->shouldReceive('with')->once()->with($input)->andReturn($validator)
                ->shouldReceive('fails')->once()->andReturn(true)
                ->shouldReceive('getMessageBag')->once()->andReturn([]);


        $this->call('POST', 'antares/settings/index', $input);
        $this->assertRedirectedTo('antares/settings/index');

        $this->assertSessionHasErrors();
    }

    /**
     * Test GET /admin/settings/migrate.
     *
     * @test
     */
    public function testGetMigrateAction()
    {
        $asset   = m::mock('\Antares\Extension\Publisher\AssetManager')->makePartial();
        $migrate = m::mock('\Antares\Extension\Publisher\MigrateManager')->makePartial();

        $asset->shouldReceive('foundation')->once()->andReturnNull();
        $migrate->shouldReceive('foundation')->once()->andReturnNull();

        $this->call('GET', 'antares/settings/migrate');
        $this->assertRedirectedTo('antares/settings/index');
    }

}
