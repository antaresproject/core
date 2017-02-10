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
 namespace Antares\Foundation\Processor\Account\TestCase;

use Mockery as m;
use Antares\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Antares\Foundation\Processor\Account\ProfileUpdater;

class ProfileUpdaterTest extends TestCase
{
    /**
     * Test Antares\Foundation\Processor\Account\ProfileUpdater::edit()
     * method.
     *
     * @test
     */
    public function testEditMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileUpdater');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Account');
        $validator = m::mock('\Antares\Foundation\Validation\Account');
        $user      = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $stub = new ProfileUpdater($presenter, $validator);

        $presenter->shouldReceive('profile')->once()->with($user, 'antares::account')->andReturnSelf();
        $listener->shouldReceive('showProfileChanger')->once()
            ->with(['eloquent' => $user, 'form' => $presenter])->andReturn('show.profile.changer');

        Auth::shouldReceive('user')->once()->andReturn($user);

        $this->assertEquals('show.profile.changer', $stub->edit($listener));
    }

    /**
     * Test Antares\Foundation\Processor\Account\ProfileUpdater::update()
     * method.
     *
     * @test
     */
    public function testUpdateMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileUpdater');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Account');
        $validator = m::mock('\Antares\Foundation\Validation\Account');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $user      = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $input = $this->getInput();

        $stub = new ProfileUpdater($presenter, $validator);

        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn($input['id'])
            ->shouldReceive('setAttribute')->once()->with('email', $input['email'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('fullname', $input['fullname'])->andReturnNull()
            ->shouldReceive('save')->once()->andReturnNull();
        $validator->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(false);
        $listener->shouldReceive('profileUpdated')->once()->andReturn('profile.updated');

        Auth::shouldReceive('user')->once()->andReturn($user);
        DB::shouldReceive('transaction')->once()
            ->with(m::type('Closure'))->andReturnUsing(function ($c) {
                $c();
            });

        $this->assertEquals('profile.updated', $stub->update($listener, $input));
    }

    /**
     * Test Antares\Foundation\Processor\Account\ProfileUpdater::update()
     * method given user mismatched.
     *
     * @test
     */
    public function testUpdateMethodGivenUserMissmatched()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileUpdater');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Account');
        $validator = m::mock('\Antares\Foundation\Validation\Account');
        $user      = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $input = $this->getInput();

        $stub = new ProfileUpdater($presenter, $validator);

        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn($input['id']++);
        $listener->shouldReceive('abortWhenUserMismatched')->once()->andReturn('user.missmatched');

        Auth::shouldReceive('user')->once()->andReturn($user);

        $this->assertEquals('user.missmatched', $stub->update($listener, $input));
    }

    /**
     * Test Antares\Foundation\Processor\Account\ProfileUpdater::update()
     * method given validation failed.
     *
     * @test
     */
    public function testUpdateMethodGivenValidationFailed()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileUpdater');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Account');
        $validator = m::mock('\Antares\Foundation\Validation\Account');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $user      = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $input = $this->getInput();

        $stub = new ProfileUpdater($presenter, $validator);

        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn($input['id']);
        $validator->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(true)
            ->shouldReceive('getMessageBag')->once()->andReturn([]);
        $listener->shouldReceive('updateProfileFailedValidation')->once()
            ->with([])->andReturn('profile.failed.validation');

        Auth::shouldReceive('user')->once()->andReturn($user);

        $this->assertEquals('profile.failed.validation', $stub->update($listener, $input));
    }

    /**
     * Test Antares\Foundation\Processor\Account\ProfileUpdater::update()
     * method given saving failed.
     *
     * @test
     */
    public function testUpdateMethodGivenSavingFailed()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileUpdater');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Account');
        $validator = m::mock('\Antares\Foundation\Validation\Account');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $user      = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $input = $this->getInput();

        $stub = new ProfileUpdater($presenter, $validator);

        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn($input['id'])
            ->shouldReceive('setAttribute')->once()->with('email', $input['email'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('fullname', $input['fullname'])->andReturnNull();
        $validator->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(false);
        $listener->shouldReceive('updateProfileFailed')->once()->with(m::type('Array'))->andReturn('profile.failed');

        Auth::shouldReceive('user')->once()->andReturn($user);
        DB::shouldReceive('transaction')->once()->with(m::type('Closure'))->andThrow('\Exception');

        $this->assertEquals('profile.failed', $stub->update($listener, $input));
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
