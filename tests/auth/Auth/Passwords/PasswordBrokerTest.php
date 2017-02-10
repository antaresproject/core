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
 namespace Antares\Auth\Passwords\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Antares\Auth\Passwords\PasswordBroker;

class PasswordBrokerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $app               = new Container();
        $app['translator'] = $translator = m::mock('\Illuminate\Translation\Translator')->makePartial();
        $translator->shouldReceive('trans')->andReturn('foo');

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Reminders\PasswordBroker::remind() method.
     *
     * @test
     */
    public function testRemindMethod()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Antares\Notifier\Handlers\Antares'),
            $view = 'foo'
        );

        $userReminderable = m::mock('\Illuminate\Contracts\Auth\CanResetPassword, \Antares\Contracts\Notification\Recipient');

        $callback = function () {
                    };

        $user->shouldReceive('retrieveByCredentials')->once()
            ->with(['username' => 'user-foo'])
            ->andReturn($userReminderable);
        $reminders->shouldReceive('create')->once()->with($userReminderable)->andReturnNull();
        $mailer->shouldReceive('send')->once()
                ->with($userReminderable, m::any(), m::type('Closure'))
                ->andReturnUsing(function ($u, $f, $c) use ($mailer) {
                    $c($mailer);
                    return true;
                });

        $this->assertEquals('passwords.sent', $stub->sendResetLink(['username' => 'user-foo'], $callback));
    }

    /**
     * Test Antares\Foundation\Reminders\PasswordBroker::remind() given
     * user is null.
     *
     * @test
     */
    public function testRemindMethodGivenUserIsNull()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Antares\Notifier\Handlers\Antares'),
            $view = 'foo'
        );

        $user->shouldReceive('retrieveByCredentials')->once()
            ->with(['username' => 'user-foo'])->andReturnNull();

        $this->assertEquals('passwords.user', $stub->sendResetLink(['username' => 'user-foo']));
    }

    /**
     * Test Antares\Foundation\Reminders\PasswordBroker::reset() method.
     *
     * @test
     */
    public function testResetMethod()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Antares\Notifier\Handlers\Antares'),
            $view = 'foo'
        );

        $userReminderable = m::mock('\Illuminate\Contracts\Auth\CanResetPassword, \Antares\Contracts\Notification\Recipient');

        $callback = function ($user, $pass) {
            return 'foo';
        };

        $credentials = [
            'username'              => 'user-foo',
            'password'              => 'qwerty',
            'password_confirmation' => 'qwerty',
            'token'                 => 'someuniquetokenkey',
        ];

        $user->shouldReceive('retrieveByCredentials')->once()
                ->with(array_except($credentials, ['token']))->andReturn($userReminderable);
        $reminders->shouldReceive('exists')->once()->with($userReminderable, 'someuniquetokenkey')->andReturn(true)
            ->shouldReceive('delete')->once()->with('someuniquetokenkey')->andReturn(true);

        $this->assertEquals('passwords.reset', $stub->reset($credentials, $callback));
    }

    /**
     * Test Antares\Foundation\Reminders\PasswordBroker::reset() method
     * given user ins not \Illuminate\Auth\Passwords\RemindableInteface.
     *
     * @test
     */
    public function testResetMethodGivenUserIsNotRemindableInterface()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Antares\Notifier\Handlers\Antares'),
            $view = 'foo'
        );

        $callback = function ($user, $pass) {
                    };

        $credentials = [
            'username'              => 'user-foo',
            'password'              => 'qwerty',
            'password_confirmation' => 'qwerty',
            'token'                 => 'someuniquetokenkey',
        ];

        $user->shouldReceive('retrieveByCredentials')->once()
            ->with(array_except($credentials, ['token']))->andReturnNull();

        $this->assertEquals('passwords.user', $stub->reset($credentials, $callback));
    }

    /**
     * Test Antares\Foundation\Reminders\PasswordBroker::reset() method
     * given fail verify password.
     *
     * @test
     */
    public function testResetMethodGivenFailVerifyPassword()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Antares\Notifier\Handlers\Antares'),
            $view = 'foo'
        );

        $callback = function ($user, $pass) {
                    };

        $credentials = [
            'username'              => 'user-foo',
            'password'              => 'qwerty',
            'password_confirmation' => 'qwerty',
            'token'                 => 'someuniquetokenkey',
        ];

        $user->shouldReceive('retrieveByCredentials')->once()
                ->with(array_except($credentials, ['token']))
                ->andReturn($userReminderable = m::mock('\Illuminate\Contracts\Auth\CanResetPassword, \Antares\Contracts\Notification\Recipient'));
        $reminders->shouldReceive('exists')->once()->with($userReminderable, 'someuniquetokenkey')->andReturn(false);

        $this->assertEquals('passwords.token', $stub->reset($credentials, $callback));
    }

    /**
     * Test Antares\Foundation\Reminders\PasswordBroker::reset() method
     * given reminder not exist.
     *
     * @test
     */
    public function testResetMethodGivenReminderNotExist()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Antares\Notifier\Handlers\Antares'),
            $view = 'foo'
        );

        $callback = function ($user, $pass) {
                    };

        $credentials = [
            'username'              => 'user-foo',
            'password'              => 'qwerty',
            'password_confirmation' => 'qwerty',
            'token'                 => 'someuniquetokenkey',
        ];

        $user->shouldReceive('retrieveByCredentials')->once()
                ->with(array_except($credentials, ['token']))
                ->andReturn($userReminderable = m::mock('\Illuminate\Contracts\Auth\CanResetPassword, \Antares\Contracts\Notification\Recipient'));
        $reminders->shouldReceive('exists')->once()->with($userReminderable, 'someuniquetokenkey')->andReturn(false);

        $this->assertEquals('passwords.token', $stub->reset($credentials, $callback));
    }

    /**
     * Test Antares\Foundation\Reminders\PasswordBroker::getUser() method
     * throws exception.
     *
     * @expectedException \UnexpectedValueException
     */
    public function testGetUserThrowsException()
    {
        $stub = new PasswordBroker(
            $reminders = m::mock('\Illuminate\Auth\Passwords\TokenRepositoryInterface'),
            $user = m::mock('\Illuminate\Contracts\Auth\UserProvider'),
            $mailer = m::mock('\Antares\Notifier\Handlers\Antares'),
            $view = 'foo'
        );

        $user->shouldReceive('retrieveByCredentials')->once()->with([])->andReturn('foo');

        $stub->getUser([]);
    }
}
