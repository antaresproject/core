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

namespace Antares\Users\Processor\Account\TestCase;

use Antares\Users\Processor\Account\ProfileUpdater;
use Antares\Testing\ApplicationTestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery as m;

class ProfileUpdaterTest extends ApplicationTestCase
{
    /**
     * Test Antares\Users\Processor\Account\ProfileUpdater::edit()
     * method.
     *
     * @test
     */
//    public function testEditMethod()
//    {
//        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileUpdater');
//        $presenter = m::mock('\Antares\Users\Http\Presenters\Account');
//        $validator = m::mock('\Antares\Users\Validation\Account');
//        $user      = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
//
//        $stub = new ProfileUpdater($presenter, $validator);
//
//        $presenter->shouldReceive('profile')->once()->with($user, 'antares::account')->andReturnSelf();
//        $listener->shouldReceive('showProfileChanger')->once()
//                ->with(['eloquent' => $user, 'form' => $presenter])->andReturn('show.profile.changer');
//
//        Auth::shouldReceive('user')->once()->andReturn($user);
//
//        $this->assertEquals('show.profile.changer', $stub->edit($listener));
//    }

    /**
     * Test Antares\Users\Processor\Account\ProfileUpdater::update()
     * method.
     *
     * @test
     */
    public function testUpdateMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileUpdater');
        $presenter = m::mock('\Antares\Users\Http\Presenters\Account');
        $validator = m::mock('\Antares\Users\Validation\Account');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $user      = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $input = $this->getInput();

        $stub = new ProfileUpdater($presenter, $validator);

        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn($input['id'])
                ->shouldReceive('setAttribute')->once()->with('email', $input['email'])->andReturnNull()
                ->shouldReceive('setAttribute')->once()->with('fullname', $input['fullname'])->andReturnNull()
                ->shouldReceive('save')->once()->andReturnNull();

        $validator->shouldReceive('with')->once()->andReturn($resolver)
                ->shouldReceive('onUpdate')->once()->andReturnSelf();

        $resolver->shouldReceive('fails')->once()->andReturn(false);
        $listener->shouldReceive('profileUpdated')->once()->andReturn('profile.updated')
                ->shouldReceive('updateProfileFailed')->once()->andReturn('profile.update.failed');

        Auth::shouldReceive('user')->once()->andReturn($user);
        DB::shouldReceive('transaction')->once()
                ->with(m::type('Closure'))->andReturnUsing(function ($c) {
            $c();
        });

        $this->assertEquals('profile.update.failed', $stub->update($listener, $input));
    }

    /**
     * Test Antares\Users\Processor\Account\ProfileUpdater::update()
     * method given user mismatched.
     *
     * @test
     */
    public function testUpdateMethodGivenUserMissmatched()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileUpdater');
        $presenter = m::mock('\Antares\Users\Http\Presenters\Account');
        $validator = m::mock('\Antares\Users\Validation\Account');
        $user      = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $input = $this->getInput();

        $stub = new ProfileUpdater($presenter, $validator);

        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn($input['id'] ++);
        $listener->shouldReceive('abortWhenUserMismatched')->once()->andReturn('user.missmatched');

        Auth::shouldReceive('user')->once()->andReturn($user);

        $this->assertEquals('user.missmatched', $stub->update($listener, $input));
    }

    /**
     * Test Antares\Users\Processor\Account\ProfileUpdater::update()
     * method given validation failed.
     *
     * @test
     */
    public function testUpdateMethodGivenValidationFailed()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileUpdater');
        $presenter = m::mock('\Antares\Users\Http\Presenters\Account');
        $validator = m::mock('\Antares\Users\Validation\Account');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $user      = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $input = $this->getInput();

        $stub = new ProfileUpdater($presenter, $validator);

        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn($input['id']);
        $validator->shouldReceive('with')->once()->andReturn($resolver)
                ->shouldReceive('onUpdate')->once()->andReturnSelf();

        $resolver->shouldReceive('fails')->once()->andReturn(true)
                ->shouldReceive('getMessageBag')->once()->andReturn([]);
        $listener->shouldReceive('updateProfileFailedValidation')->once()
                ->with([])->andReturn('profile.failed.validation');

        Auth::shouldReceive('user')->once()->andReturn($user);

        $this->assertEquals('profile.failed.validation', $stub->update($listener, $input));
    }

    /**
     * Test Antares\Users\Processor\Account\ProfileUpdater::update()
     * method given saving failed.
     *
     * @test
     */
    public function testUpdateMethodGivenSavingFailed()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileUpdater');
        $presenter = m::mock('\Antares\Users\Http\Presenters\Account');
        $validator = m::mock('\Antares\Users\Validation\Account');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $user      = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $input = $this->getInput();

        $stub = new ProfileUpdater($presenter, $validator);

        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn($input['id'])
                ->shouldReceive('setAttribute')->once()->with('email', $input['email'])->andReturnNull()
                ->shouldReceive('setAttribute')->once()->with('fullname', $input['fullname'])->andReturnNull();
        $validator->shouldReceive('with')->once()->andReturn($resolver)
                ->shouldReceive('onUpdate')->once()->andReturnSelf();
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
