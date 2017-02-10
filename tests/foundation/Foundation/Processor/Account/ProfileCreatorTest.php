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
use Illuminate\Support\Facades\Config;
use Antares\Support\Facades\Foundation;
use Antares\Foundation\Processor\Account\ProfileCreator;

class ProfileCreatorTest extends TestCase
{
    /**
     * Test Antares\Foundation\Processor\Account\ProfileCreator::create()
     * method.
     *
     * @test
     */
    public function testCreateMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileCreator');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Account');
        $validator = m::mock('\Antares\Foundation\Validation\Account');
        $user      = m::mock('\Antares\Model\User');
        $form      = m::mock('\Antares\Contracts\Html\Form\Builder');

        $stub = new ProfileCreator($presenter, $validator);

        $presenter->shouldReceive('profile')->once()
            ->with($user, 'antares::register')->andReturn($form);
        $form->shouldReceive('extend')->once()->with(m::type('Closure'))
            ->andReturnUsing(function ($c) use ($form) {
                return $c($form);
            });
        $listener->shouldReceive('showProfileCreator')->once()
            ->with(['eloquent' => $user, 'form' => $form])->andReturn('profile.create');

        Foundation::shouldReceive('make')->once()->with('antares.user')->andReturn($user);

        $this->assertEquals('profile.create', $stub->create($listener));
    }

    /**
     * Test Antares\Foundation\Processor\Account\ProfileCreator::store()
     * method.
     *
     * @test
     */
    public function testStoreMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileCreator');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Account');
        $validator = m::mock('\Antares\Foundation\Validation\Account');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $receipt   = m::mock('\Antares\Contracts\Notification\Receipt');
        $memory    = m::mock('\Antares\Contracts\Memory\Provider');
        $user      = m::mock('\Antares\Model\User');
        $role      = m::mock('\Antares\Model\Role');

        $input = $this->getInput();

        $stub = new ProfileCreator($presenter, $validator);

        $validator->shouldReceive('on')->once()->with('register')->andReturnSelf()
            ->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(false);
        $user->shouldReceive('setAttribute')->once()->with('email', $input['email'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('fullname', $input['fullname'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('password', m::type('String'))->andReturnNull()
            ->shouldReceive('save')->once()->andReturnNull()
            ->shouldReceive('roles')->once()->andReturn($role)
            ->shouldReceive('toArray')->once()->andReturn([])
            ->shouldReceive('notify')->once()
                ->with(m::type('\Antares\Contracts\Notification\Message'))
                ->andReturn($receipt);
        $role->shouldReceive('sync')->once()->with([2])->andReturnNull();
        $memory->shouldReceive('get')->once()->with('site.name', 'Antares Platform')->andReturn('Antares Platform');
        $receipt->shouldReceive('failed')->once()->andReturn(false);
        $listener->shouldReceive('profileCreated')->once()->andReturn('profile.created');

        Config::shouldReceive('get')->once()->with('antares/foundation::roles.member', 2)->andReturn(2);
        DB::shouldReceive('transaction')->once()->with(m::type('Closure'))
            ->andReturnUsing(function ($c) {
                return $c();
            });
        Foundation::shouldReceive('make')->once()->with('antares.user')->andReturn($user);
        Foundation::shouldReceive('memory')->once()->andReturn($memory);

        $this->assertEquals('profile.created', $stub->store($listener, $input));
    }

    /**
     * Test Antares\Foundation\Processor\Account\ProfileCreator::store()
     * method with failed notification.
     *
     * @test
     */
    public function testStoreMethodGivenFailedNotification()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileCreator');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Account');
        $validator = m::mock('\Antares\Foundation\Validation\Account');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $receipt   = m::mock('\Antares\Contracts\Notification\Receipt');
        $memory    = m::mock('\Antares\Contracts\Memory\Provider');
        $user      = m::mock('\Antares\Model\User');
        $role      = m::mock('\Antares\Model\Role');

        $input = $this->getInput();

        $stub = new ProfileCreator($presenter, $validator);

        $validator->shouldReceive('on')->once()->with('register')->andReturnSelf()
            ->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(false);
        $user->shouldReceive('setAttribute')->once()->with('email', $input['email'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('fullname', $input['fullname'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('password', m::type('String'))->andReturnNull()
            ->shouldReceive('save')->once()->andReturnNull()
            ->shouldReceive('roles')->once()->andReturn($role)
            ->shouldReceive('toArray')->once()->andReturn([])
            ->shouldReceive('notify')->once()
                ->with(m::type('\Antares\Contracts\Notification\Message'))
                ->andReturn($receipt);
        $role->shouldReceive('sync')->once()->with([2])->andReturnNull();
        $memory->shouldReceive('get')->once()->with('site.name', 'Antares Platform')->andReturn('Antares Platform');
        $receipt->shouldReceive('failed')->once()->andReturn(true);
        $listener->shouldReceive('profileCreatedWithoutNotification')->once()->andReturn('profile.created.without.notification');

        Config::shouldReceive('get')->once()->with('antares/foundation::roles.member', 2)->andReturn(2);
        DB::shouldReceive('transaction')->once()->with(m::type('Closure'))
            ->andReturnUsing(function ($c) {
                return $c();
            });
        Foundation::shouldReceive('make')->once()->with('antares.user')->andReturn($user);
        Foundation::shouldReceive('memory')->once()->andReturn($memory);

        $this->assertEquals('profile.created.without.notification', $stub->store($listener, $input));
    }

    /**
     * Test Antares\Foundation\Processor\Account\ProfileCreator::store()
     * method with failed saving to db.
     *
     * @test
     */
    public function testStoreMethodGivenFailedSavingToDB()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileCreator');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Account');
        $validator = m::mock('\Antares\Foundation\Validation\Account');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');
        $user      = m::mock('\Antares\Model\User');

        $input = $this->getInput();

        $stub = new ProfileCreator($presenter, $validator);

        $validator->shouldReceive('on')->once()->with('register')->andReturnSelf()
            ->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(false);
        $user->shouldReceive('setAttribute')->once()->with('email', $input['email'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('fullname', $input['fullname'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('password', m::type('String'))->andReturnNull();
        $listener->shouldReceive('createProfileFailed')->once()->with(m::type('Array'))->andReturn('profile.failed');

        DB::shouldReceive('transaction')->once()->with(m::type('Closure'))->andThrow('\Exception');
        Foundation::shouldReceive('make')->once()->with('antares.user')->andReturn($user);

        $this->assertEquals('profile.failed', $stub->store($listener, $input));
    }

    /**
     * Test Antares\Foundation\Processor\Account\ProfileCreator::store()
     * method with failed validation.
     *
     * @test
     */
    public function testStoreMethodGivenFailedValidation()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileCreator');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Account');
        $validator = m::mock('\Antares\Foundation\Validation\Account');
        $resolver  = m::mock('\Illuminate\Contracts\Validation\Validator');

        $input = $this->getInput();

        $stub = new ProfileCreator($presenter, $validator);

        $validator->shouldReceive('on')->once()->with('register')->andReturnSelf()
            ->shouldReceive('with')->once()->with($input)->andReturn($resolver);
        $resolver->shouldReceive('fails')->once()->andReturn(true)
            ->shouldReceive('getMessageBag')->once()->andReturn([]);
        $listener->shouldReceive('createProfileFailedValidation')->once()
            ->with(m::type('Array'))->andReturn('profile.failed.validation');

        $this->assertEquals('profile.failed.validation', $stub->store($listener, $input));
    }

    /**
     * Get request input.
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
