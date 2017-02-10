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
 namespace Antares\Foundation\Http\Controllers\Account\TestCase;

use Mockery as m;
use Antares\Testing\TestCase;
use Illuminate\Support\Facades\View;
use Antares\Support\Facades\Messages;
use Antares\Support\Facades\Foundation;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class PasswordUpdaterControllerTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->disableMiddlewareForAllTests();
    }

    /**
     * Test GET /admin/account.
     *
     * @test
     */
    public function testGetEditAction()
    {
        $this->getProcessorMock()->shouldReceive('edit')->once()
            ->with(m::type('\Antares\Foundation\Http\Controllers\Account\PasswordUpdaterController'))
            ->andReturnUsing(function ($listener) {
                return $listener->showPasswordChanger([]);
            });

        View::shouldReceive('make')->once()
            ->with('antares/foundation::account.password', [], [])->andReturn('show.password.changer');

        $this->call('GET', 'admin/account/password');
        $this->assertResponseOk();
    }

    /**
     * Test POST /admin/account.
     *
     * @test
     */
    public function testPostUpdateAction()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
            ->with(m::type('\Antares\Foundation\Http\Controllers\Account\PasswordUpdaterController'), $input)
            ->andReturnUsing(function ($listener) {
                return $listener->passwordUpdated([]);
            });

        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        Foundation::shouldReceive('handles')->once()->with('antares::account/password', [])->andReturn('password');

        $this->call('POST', 'admin/account/password', $input);
        $this->assertRedirectedTo('password');
    }

    /**
     * Test POST /admin/account with invalid user id.
     *
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testPostIndexActionGivenInvalidUserId()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
            ->with(m::type('\Antares\Foundation\Http\Controllers\Account\PasswordUpdaterController'), $input)
            ->andReturnUsing(function ($listener) {
                return $listener->abortWhenUserMismatched();
            });

        $this->call('POST', 'admin/account/password', $input);
        $this->assertRedirectedTo('password');
    }

    /**
     * Test POST /admin/account with database error.
     *
     * @test
     */
    public function testPostIndexActionGivenDatabaseError()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
            ->with(m::type('\Antares\Foundation\Http\Controllers\Account\PasswordUpdaterController'), $input)
            ->andReturnUsing(function ($listener) {
                return $listener->updatePasswordFailed([]);
            });

        Foundation::shouldReceive('handles')->once()->with('antares::account/password', [])->andReturn('password');
        Messages::shouldReceive('add')->once()->with('error', m::any())->andReturnNull();

        $this->call('POST', 'admin/account/password', $input);
        $this->assertRedirectedTo('password');
    }

    /**
     * Test POST /admin/account with validation failed.
     *
     * @test
     */
    public function testPostIndexActionGivenValidationFailed()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
            ->with(m::type('\Antares\Foundation\Http\Controllers\Account\PasswordUpdaterController'), $input)
            ->andReturnUsing(function ($listener) {
                return $listener->updatePasswordFailedValidation([]);
            });

        Foundation::shouldReceive('handles')->once()->with('antares::account/password', [])->andReturn('password');

        $this->call('POST', 'admin/account/password', $input);
        $this->assertRedirectedTo('password');
    }

    /**
     * Test POST /admin/account with hash check failed.
     *
     * @test
     */
    public function testPostIndexActionGivenHashMissmatch()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
            ->with(m::type('\Antares\Foundation\Http\Controllers\Account\PasswordUpdaterController'), $input)
            ->andReturnUsing(function ($listener) {
                return $listener->verifyCurrentPasswordFailed([]);
            });

        Foundation::shouldReceive('handles')->once()->with('antares::account/password', [])->andReturn('password');

        $this->call('POST', 'admin/account/password', $input);
        $this->assertRedirectedTo('password');
    }

    /**
     * Get processor mock.
     *
     * @return \Antares\Foundation\Processor\Account\PasswordUpdater
     */
    protected function getProcessorMock()
    {
        $processor = m::mock('\Antares\Foundation\Processor\Account\PasswordUpdater');

        $this->app->instance('Antares\Foundation\Processor\Account\PasswordUpdater', $processor);

        return $processor;
    }

    /**
     * Get sample input.
     *
     * @return array
     */
    protected function getInput()
    {
        return [
            'id'               => '1',
            'current_password' => '123456',
            'new_password'     => 'qwerty',
        ];
    }
}
