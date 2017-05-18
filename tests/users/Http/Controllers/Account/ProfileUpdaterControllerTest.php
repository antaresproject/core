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
use Antares\Testing\ApplicationTestCase;
use Antares\Support\Facades\Messages;
use Illuminate\Support\Facades\View;
use Mockery as m;

class ProfileUpdaterControllerTest extends ApplicationTestCase
{

    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $this->disableMiddlewareForAllTests();
        $this->app[\Illuminate\Contracts\Auth\Factory::class] = $auth                                                 = m::mock(\Illuminate\Contracts\Auth\Factory::class);
        $auth->shouldReceive('guest')->andReturn(false)
                ->shouldReceive('user')->once()->andReturn(\Antares\Model\User::query()->whereId(1)->first());
    }

    /**
     * Test GET /antares/account.
     *
     * @test
     */
    public function testGetEditAction()
    {
        $this->app[\Illuminate\Contracts\Auth\Factory::class] = $auth                                                 = m::mock(\Illuminate\Contracts\Auth\Factory::class);
        $auth->shouldReceive('guest')->andReturn(false)
                ->shouldReceive('user')->once()->andReturn(\Antares\Model\User::query()->whereId(1)->first());

        $this->getProcessorMock()->shouldReceive('edit')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\ProfileUpdaterController'))
                ->andReturnUsing(function ($listener) {
                    return $listener->showProfileChanger([]);
                });

        View::shouldReceive('make')->once()->withAnyArgs()->andReturnSelf()
                ->shouldReceive('addNamespace')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('render')->withAnyArgs()->andReturn('show.profile.changer')
                ->shouldReceive('share')->withAnyArgs()->andReturnSelf();
        $this->call('GET', 'antares/account');
        $this->assertResponseOk();
    }

    /**
     * Test POST /antares/account.
     *
     * @test
     */
    public function testPostUpdateAction()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\ProfileUpdaterController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->profileUpdated([]);
                });

        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        $this->call('POST', 'antares/account', $input);
        $this->assertRedirectedTo('administrators/account');
    }

    /**
     * Test POST /antares/account with invalid user id.
     *
     */
    public function testPostIndexActionGivenInvalidUserId()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\ProfileUpdaterController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->abortWhenUserMismatched();
                });

        $this->call('POST', 'antares/account', $input);
        $this->assertResponseStatus(302);
    }

    /**
     * Test POST /antares/account with database error.
     *
     * @test
     */
    public function testPostIndexActionGivenDatabaseError()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\ProfileUpdaterController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->updateProfileFailed([]);
                });

        Messages::shouldReceive('add')->once()->with('error', m::any())->andReturnNull();

        $this->call('POST', 'antares/account', $input);
        $this->assertRedirectedTo('administrators/account');
    }

    /**
     * Test POST /antares/account with validation failed.
     *
     * @test
     */
    public function testPostIndexActionGivenValidationFailed()
    {
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('update')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\ProfileUpdaterController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->updateProfileFailedValidation([]);
                });

        $this->call('POST', 'antares/account', $input);
        $this->assertRedirectedTo('administrators/account');
    }

    /**
     * Get processor mock.
     *
     * @return \Antares\Users\Processor\Account\ProfileUpdater
     */
    protected function getProcessorMock()
    {
        $processor = m::mock('\Antares\Users\Processor\Account\ProfileUpdater');

        $this->app->instance('\Antares\Users\Processor\Account\ProfileUpdater', $processor);

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
            'id'       => '1',
            'email'    => 'email@antaresplatform.com',
            'fullname' => 'Administrator',
        ];
    }

}
