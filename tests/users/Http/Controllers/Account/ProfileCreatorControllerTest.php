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

namespace Antares\Users\Http\Controllers\Account\TestCase;

use Mockery as m;
use Antares\Testing\TestCase;
use Illuminate\Support\Facades\View;
use Antares\Support\Facades\Messages;
use Antares\Support\Facades\Foundation;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ProfileCreatorControllerTest extends TestCase
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
     * Test GET /antares/register.
     *
     * @test
     */
    public function testGetCreateAction()
    {
        $this->markTestIncomplete('Registration is not currently supported.');

        $this->getProcessorMock()->shouldReceive('create')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\ProfileCreatorController'))
                ->andReturnUsing(function ($listener) {
                    return $listener->showProfileCreator([]);
                });

        View::shouldReceive('make')->once()
                ->with('antares/foundation::credential.register', [], [])->andReturn('foo');

        $this->call('GET', 'antares/register');
        $this->assertResponseOk();
    }

    /**
     * Test POST /antares/register.
     *
     * @test
     */
    public function testPostStoreAction()
    {
        $this->markTestIncomplete('Registration is not currently supported.');


        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('store')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\ProfileCreatorController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->profileCreated();
                });

        Foundation::shouldReceive('handles')->once()->with('antares::login', [])->andReturn('login');
        Messages::shouldReceive('add')->twice()->with('success', m::any())->andReturnNull();

        $this->call('POST', 'antares/register', $input);
        $this->assertRedirectedTo('login');
    }

    /**
     * Test POST /antares/register failed to send email.
     *
     * @test
     */
    public function testPostStoreActionGivenFailedToSendEmail()
    {
        $this->markTestIncomplete('Registration is not currently supported.');
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('store')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\ProfileCreatorController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->profileCreatedWithoutNotification();
                });

        Foundation::shouldReceive('handles')->once()->with('antares::login', [])->andReturn('login');
        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        Messages::shouldReceive('add')->once()->with('error', m::any())->andReturnNull();

        $this->call('POST', 'antares/register', $input);
        $this->assertRedirectedTo('login');
    }

    /**
     * Test POST /antares/register with database error.
     *
     * @test
     */
    public function testPostStoreActionGivenDatabaseError()
    {
        $this->markTestIncomplete('Registration is not currently supported.');
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('store')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\ProfileCreatorController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->createProfileFailed(['error' => '']);
                });

        Foundation::shouldReceive('handles')->once()->with('antares::register', [])->andReturn('register');
        Messages::shouldReceive('add')->once()->with('error', m::any())->andReturnNull();

        $this->call('POST', 'antares/register', $input);
        $this->assertRedirectedTo('register');
    }

    /**
     * Test POST /antares/register with failed validation.
     *
     * @test
     */
    public function testPostStoreActionGivenFailedValidation()
    {
        $this->markTestIncomplete('Registration is not currently supported.');
        $input = $this->getInput();

        $this->getProcessorMock()->shouldReceive('store')->once()
                ->with(m::type('\Antares\Users\Http\Controllers\Account\ProfileCreatorController'), $input)
                ->andReturnUsing(function ($listener) {
                    return $listener->createProfileFailedValidation([]);
                });

        Foundation::shouldReceive('handles')->once()->with('antares::register', [])->andReturn('register');

        $this->call('POST', 'antares/register', $input);
        $this->assertRedirectedTo('register');
    }

    /**
     * Get processor mock.
     *
     * @return Antares\Users\Processor\Account\ProfileCreator
     */
    protected function getProcessorMock()
    {
        $processor = m::mock('Antares\Users\Processor\Account\ProfileCreator');

        $this->app->instance('Antares\Users\Processor\Account\ProfileCreator', $processor);

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
            'email'    => 'email@antaresplatform.com',
            'fullname' => 'Administrator',
        ];
    }

}
