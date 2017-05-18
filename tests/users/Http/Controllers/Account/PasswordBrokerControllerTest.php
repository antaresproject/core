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

namespace Antares\Users\Http\Controllers\Account\TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Contracts\Auth\PasswordBroker;
use Antares\Testing\ApplicationTestCase;
use Antares\Support\Facades\Messages;
use Illuminate\Support\Facades\View;
use Mockery as m;

class PasswordBrokerControllerTest extends ApplicationTestCase
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
     * Test GET /antares/forgot.
     *
     * @test
     */
    public function testGetCreateAction()
    {
        $this->getProcessorMock();
        View::shouldReceive('make')->once()->with('antares/foundation::forgot.index', [], [])->andReturn('foo');
        $this->call('GET', 'antares/forgot');
        $this->assertResponseOk();
    }

    /**
     * Test POST /antares/forgot.
     *
     * @test
     */
    public function testPostStoreAction()
    {
        $input = [
            'email' => 'email@antaresplatform.com',
        ];

        $this->getProcessorMock()->shouldReceive('store')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\PasswordBrokerController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->resetLinkSent(PasswordBroker::RESET_LINK_SENT);
                });

        Messages::shouldReceive('add')->once()->with('success', trans(PasswordBroker::RESET_LINK_SENT))->andReturnNull();

        $this->call('POST', 'antares/forgot', $input);
        $this->assertRedirectedTo('antares/forgot');
    }

    /**
     * Test POST /antares/forgot given invalid user.
     *
     * @test
     */
    public function testPostStoreActionGivenInvalidUser()
    {
        $input = [
            'email' => 'email@antaresplatform.com',
        ];

        $this->getProcessorMock()->shouldReceive('store')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\PasswordBrokerController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->resetLinkFailed(PasswordBroker::INVALID_USER);
                });

        Messages::shouldReceive('add')->once()->with('error', trans(PasswordBroker::INVALID_USER))->andReturnNull();

        $this->call('POST', 'antares/forgot', $input);
        $this->assertRedirectedTo('antares/forgot');
    }

    /**
     * Test POST /antares/forgot when validation fails.
     *
     * @test
     */
    public function testPostStoreActionGivenFailedValidation()
    {
        $input = [
            'email' => 'email@antaresplatform.com',
        ];

        $this->getProcessorMock()->shouldReceive('store')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\PasswordBrokerController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->resetLinkFailedValidation([]);
                });



        $this->call('POST', 'antares/forgot', $input);
        $this->assertRedirectedTo('antares/forgot');
        $this->assertSessionHas('errors');
    }

    /**
     * Test GET /antares/forgot/reset.
     *
     * @test
     */
    public function testGetEditAction()
    {
        $this->getProcessorMock();

        $view = m::mock('\Illuminate\Contracts\View\View');

        View::shouldReceive('make')->once()->with('antares/foundation::forgot.reset', [], [])->andReturn($view);
        $view->shouldReceive('with')->once()->with('token', 'auniquetoken')->andReturn('foo');

        $this->call('GET', 'antares/forgot/reset/auniquetoken');
        $this->assertResponseOk();
    }

    /**
     * Test GET /antares/forgot/reset given token is null.
     */
    public function testGetEditActionGivenTokenIsNull()
    {
        $this->call('GET', 'antares/forgot/reset');
        $this->assertResponseStatus(405);
    }

    /**
     * Test POST /antares/forgot/reset.
     *
     * @test
     */
    public function testPostUpdateAction()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\PasswordBrokerController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->passwordHasReset(PasswordBroker::PASSWORD_RESET);
                });

        Messages::shouldReceive('add')->once()->with('success', m::type('String'))->andReturnNull();

        $this->call('POST', 'antares/forgot/reset', $input);
        $this->assertRedirectedTo('antares');
    }

    /**
     * Test POST /antares/forgot/reset given invalid password.
     *
     * @test
     */
    public function testPostUpdateActionGivenInvalidPassword()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\PasswordBrokerController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->passwordResetHasFailed(PasswordBroker::INVALID_PASSWORD);
                });

        Messages::shouldReceive('add')->once()->with('error', trans(PasswordBroker::INVALID_PASSWORD))->andReturnNull();

        $this->call('POST', 'antares/forgot/reset', $input);
        $this->assertRedirectedTo('antares/forgot/reset/auniquetoken');
    }

    /**
     * Test POST /antares/forgot/reset given invalid token.
     *
     * @test
     */
    public function testPostUpdateActionGivenTokenIsInvalid()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\PasswordBrokerController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->passwordResetHasFailed(PasswordBroker::INVALID_TOKEN);
                });

        Messages::shouldReceive('add')->once()->with('error', trans(PasswordBroker::INVALID_TOKEN))->andReturnNull();

        $this->call('POST', 'antares/forgot/reset', $input);
        $this->assertRedirectedTo('antares/forgot/reset/auniquetoken');
    }

    /**
     * Test POST /antares/forgot/reset given invalid user.
     *
     * @test
     */
    public function testPostUpdateActionGivenInvalidUser()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\PasswordBrokerController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->passwordResetHasFailed(PasswordBroker::INVALID_USER);
                });

        Messages::shouldReceive('add')->once()->with('error', trans(PasswordBroker::INVALID_USER))->andReturnNull();

        $this->call('POST', 'antares/forgot/reset', $input);
        $this->assertRedirectedTo('antares/forgot/reset/auniquetoken');
    }

    /**
     * Get processor mock.
     *
     * @return Antares\Users\Processor\Account\PasswordBroker
     */
    protected function getProcessorMock()
    {
        $processor = m::mock('Antares\Users\Processor\Account\PasswordBroker');

        $this->app->instance('Antares\Users\Processor\Account\PasswordBroker', $processor);

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
            'email'                 => 'email@antaresplatform.com',
            'password'              => '123456',
            'password_confirmation' => '123456',
            'token'                 => 'auniquetoken',
        ];
    }

}
