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
 namespace Antares\Foundation\Http\Controllers\TestCase;

use Mockery as m;
use Antares\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Antares\Support\Facades\Messages;
use Antares\Support\Facades\Foundation;

class UsersControllerTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        View::shouldReceive('addNamespace');
        View::shouldReceive('share')->once()->with('errors', m::any());

        $this->app['Illuminate\Contracts\Auth\Guard'] = $auth = m::mock('\Illuminate\Contracts\Auth\Guard');
        $this->app['Illuminate\Contracts\Auth\Authenticatable'] = $user = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $auth->shouldReceive('user')->andReturn($user);
    }

    /**
     * Bind dependencies.
     *
     * @return array
     */
    protected function bindDependencies()
    {
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\User');
        $validator = m::mock('\Antares\Foundation\Validation\User');

        App::instance('Antares\Foundation\Http\Presenters\User', $presenter);
        App::instance('Antares\Foundation\Validation\User', $validator);

        return [$presenter, $validator];
    }

    /**
     * Test GET /admin/users.
     *
     * @test
     */
    public function testGetIndexAction()
    {
        $user  = m::mock('\Antares\Model\User');
        $role  = m::mock('\Antares\Model\Role');
        $table = m::mock('\Antares\Contracts\Html\Table\Builder');

        list($presenter, ) = $this->bindDependencies();

        $user->shouldReceive('search')->once()->with('', [])->andReturn($user);
        $role->shouldReceive('lists')->once()->with('name', 'id')->andReturn([]);
        $presenter->shouldReceive('table')->once()->andReturn($table)
            ->shouldReceive('actions')->once()->with($table)->andReturn('list.users');

        Foundation::shouldReceive('make')->once()->with('antares.user')->andReturn($user);
        Foundation::shouldReceive('make')->once()->with('antares.role')->andReturn($role);
        View::shouldReceive('make')->once()
            ->with('antares/foundation::users.index', m::type('Array'), [])->andReturn('foo');

        $this->call('GET', 'admin/users');
        $this->assertResponseOk();
    }

    /**
     * Test GET /admin/users/create.
     *
     * @test
     */
    public function testGetCreateAction()
    {
        list($presenter, ) = $this->bindDependencies();

        $presenter->shouldReceive('form')->once()->andReturn('form.users');

        Foundation::shouldReceive('make')->once()->with('antares.user')->andReturn([]);
        View::shouldReceive('make')->once()
            ->with('antares/foundation::users.edit', m::type('Array'), [])->andReturn('foo');

        $this->call('GET', 'admin/users/create');
        $this->assertResponseOk();
    }

    /**
     * Test GET /admin/users/(:any)/edit.
     *
     * @test
     */
    public function testGetEditAction()
    {
        $builder = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $user    = m::mock('\Antares\Model\User');

        list($presenter, ) = $this->bindDependencies();

        $builder->shouldReceive('findOrFail')->once()->with('foo')->andReturn($user);
        $presenter->shouldReceive('form')->once()->andReturn('form.users');

        Foundation::shouldReceive('make')->once()->with('antares.user')->andReturn($builder);
        View::shouldReceive('make')->once()
            ->with('antares/foundation::users.edit', m::type('Array'), [])->andReturn('foo');

        $this->call('GET', 'admin/users/foo/edit');
        $this->assertResponseOk();
    }

    /**
     * Test POST /admin/users.
     *
     * @test
     */
    public function testPostStoreAction()
    {
        $input = [
            'email'    => 'email@antaresplatform.com',
            'fullname' => 'Administrator',
            'password' => '123456',
            'roles'    => [1],
        ];

        list(, $validator) = $this->bindDependencies();

        $user = m::mock('\Antares\Model\User');

        $user->shouldReceive('setAttribute')->once()->with('status', 0)->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('password', $input['password'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('email', $input['email'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('fullname', $input['fullname'])->andReturnNull()
            ->shouldReceive('save')->once()->andReturnNull()
            ->shouldReceive('roles')->once()->andReturn($user)
            ->shouldReceive('sync')->once()->with($input['roles'])->andReturnNull();
        $validator->shouldReceive('on')->once()->with('create')->andReturn($validator)
            ->shouldReceive('with')->once()->with($input)->andReturn($validator)
            ->shouldReceive('fails')->once()->andReturnNull();

        Foundation::shouldReceive('make')->once()->with('antares.user')->andReturn($user);
        Foundation::shouldReceive('handles')->once()->with('antares::users', [])->andReturn('users');
        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        DB::shouldReceive('transaction')->once()
            ->with(m::type('Closure'))->andReturnUsing(function ($c) {
                $c();
            });

        $this->call('POST', 'admin/users', $input);
        $this->assertRedirectedTo('users');
    }

    /**
     * Test POST /admin/users when database error.
     *
     * @test
     */
    public function testPostStoreActionGivenDatabaseError()
    {
        $input = [
            'email'    => 'email@antaresplatform.com',
            'fullname' => 'Administrator',
            'password' => '123456',
            'roles'    => [1],
        ];

        list(, $validator) = $this->bindDependencies();

        $user = m::mock('\Antares\Model\User');

        $user->shouldReceive('setAttribute')->once()->with('status', 0)->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('password', $input['password'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('email', $input['email'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('fullname', $input['fullname'])->andReturnNull()
            ->shouldReceive('save')->once()->andReturnNull()
            ->shouldReceive('roles')->once()->andReturn($user)
            ->shouldReceive('sync')->once()->with($input['roles'])->andThrow('\Exception');
        $validator->shouldReceive('on')->once()->with('create')->andReturn($validator)
            ->shouldReceive('with')->once()->with($input)->andReturn($validator)
            ->shouldReceive('fails')->once()->andReturnNull();

        Foundation::shouldReceive('make')->once()->with('antares.user')->andReturn($user);
        Foundation::shouldReceive('handles')->once()->with('antares::users', [])->andReturn('users');
        Messages::shouldReceive('add')->once()->with('error', m::any())->andReturnNull();
        DB::shouldReceive('transaction')->once()
            ->with(m::type('Closure'))->andReturnUsing(function ($c) {
                $c();
            });

        $this->call('POST', 'admin/users', $input);
        $this->assertRedirectedTo('users');
    }

    /**
     * Test POST /admin/users when validation error.
     *
     * @test
     */
    public function testPostStoreActionGivenValidationError()
    {
        $input = [
            'email'    => 'email@antaresplatform.com',
            'fullname' => 'Administrator',
            'password' => '123456',
            'roles'    => [1],
        ];

        list(, $validator) = $this->bindDependencies();

        $validator->shouldReceive('on')->once()->with('create')->andReturn($validator)
            ->shouldReceive('with')->once()->with($input)->andReturn($validator)
            ->shouldReceive('getMessageBag')->once()->andReturn([])
            ->shouldReceive('fails')->once()->andReturn(true);

        Foundation::shouldReceive('handles')->once()->with('antares::users/create', [])->andReturn('users/create');

        $this->call('POST', 'admin/users', $input);
        $this->assertRedirectedTo('users/create');
        $this->assertSessionHasErrors();
    }

    /**
     * Test PUT /admin/users/(:any).
     *
     * @test
     */
    public function testPutUpdateAction()
    {
        $input = [
            'id'       => 'foo',
            'email'    => 'email@antaresplatform.com',
            'fullname' => 'Administrator',
            'password' => '123456',
            'roles'    => [1],
        ];

        list(, $validator) = $this->bindDependencies();

        $builder = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $user    = m::mock('\Antares\Model\User');

        $builder->shouldReceive('findOrFail')->once()->with('foo')->andReturn($user);
        $user->shouldReceive('setAttribute')->once()->with('password', $input['password'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('email', $input['email'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('fullname', $input['fullname'])->andReturnNull()
            ->shouldReceive('save')->once()->andReturnNull()
            ->shouldReceive('roles')->once()->andReturn($user)
            ->shouldReceive('sync')->once()->with($input['roles'])->andReturnNull();
        $validator->shouldReceive('on')->once()->with('update')->andReturn($validator)
            ->shouldReceive('with')->once()->with($input)->andReturn($validator)
            ->shouldReceive('fails')->once()->andReturnNull();

        Foundation::shouldReceive('make')->once()->with('antares.user')->andReturn($builder);
        Foundation::shouldReceive('handles')->once()->with('antares::users', [])->andReturn('users');
        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        DB::shouldReceive('transaction')->once()
            ->with(m::type('Closure'))->andReturnUsing(function ($c) {
                $c();
            });

        $this->call('PUT', 'admin/users/foo', $input);
        $this->assertRedirectedTo('users');
    }

    /**
     * Test PUT /admin/users/(:any) when invalid user id is given.
     *
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testPutUpdateActionGivenInvalidUserId()
    {
        $input = [
            'id'       => 'foo',
            'email'    => 'email@antaresplatform.com',
            'fullname' => 'Administrator',
            'password' => '123456',
            'roles'    => [1],
        ];

        $this->call('PUT', 'admin/users/foobar', $input);
    }

    /**
     * Test PUT /admin/users/(:any) when database error.
     *
     * @test
     */
    public function testPutUpdateActionGivenDatabaseError()
    {
        $input = [
            'id'       => 'foo',
            'email'    => 'email@antaresplatform.com',
            'fullname' => 'Administrator',
            'password' => '123456',
            'roles'    => [1],
        ];

        list(, $validator) = $this->bindDependencies();

        $builder = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $user    = m::mock('\Antares\Model\User');

        $builder->shouldReceive('findOrFail')->once()->with('foo')->andReturn($user);
        $user->shouldReceive('setAttribute')->once()->with('password', $input['password'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('email', $input['email'])->andReturnNull()
            ->shouldReceive('setAttribute')->once()->with('fullname', $input['fullname'])->andReturnNull()
            ->shouldReceive('save')->once()->andReturnNull()
            ->shouldReceive('roles')->once()->andReturn($user)
            ->shouldReceive('sync')->once()->with($input['roles'])->andThrow('\Exception');
        $validator->shouldReceive('on')->once()->with('update')->andReturn($validator)
            ->shouldReceive('with')->once()->with($input)->andReturn($validator)
            ->shouldReceive('fails')->once()->andReturnNull();

        Foundation::shouldReceive('make')->once()
            ->with('antares.user')->andReturn($builder);
        Foundation::shouldReceive('handles')->once()
            ->with('antares::users', [])->andReturn('users');
        Messages::shouldReceive('add')->once()
            ->with('error', m::any())->andReturnNull();
        DB::shouldReceive('transaction')->once()
            ->with(m::type('Closure'))->andReturnUsing(function ($c) {
                $c();
            });

        $this->call('PUT', 'admin/users/foo', $input);
        $this->assertRedirectedTo('users');
    }

    /**
     * Test PUT /admin/users/(:any) when validation error.
     *
     * @test
     */
    public function testPutUpdateActionGivenValidationError()
    {
        $input = [
            'id'       => 'foo',
            'email'    => 'email@antaresplatform.com',
            'fullname' => 'Administrator',
            'password' => '123456',
            'roles'    => [1],
        ];

        list(, $validator) = $this->bindDependencies();

        $validator->shouldReceive('on')->once()->with('update')->andReturn($validator)
            ->shouldReceive('with')->once()->with($input)->andReturn($validator)
            ->shouldReceive('getMessageBag')->once()->andReturn([])
            ->shouldReceive('fails')->once()->andReturn(true);

        Foundation::shouldReceive('handles')->once()
            ->with('antares::users/foo/edit', [])->andReturn('users/foo/edit');

        $this->call('PUT', 'admin/users/foo', $input);
        $this->assertRedirectedTo('users/foo/edit');
        $this->assertSessionHasErrors();
    }

    /**
     * Test GET /admin/users/(:any)/delete.
     *
     * @test
     */
    public function testGetDeleteAction()
    {
        $builder = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $user    = m::mock('\Antares\Model\User');
        $auth    = (object) [
            'id' => 'foobar',
        ];

        $builder->shouldReceive('findOrFail')->once()->with('foo')->andReturn($user);
        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn('foo')
            ->shouldReceive('delete')->once()->andReturnNull();

        Foundation::shouldReceive('make')->once()
            ->with('antares.user')->andReturn($builder);
        Foundation::shouldReceive('handles')->once()
            ->with('antares::users', [])->andReturn('users');
        Messages::shouldReceive('add')->once()
            ->with('success', m::any())->andReturnNull();
        Auth::shouldReceive('user')->once()->andReturn($auth);
        DB::shouldReceive('transaction')->once()
            ->with(m::type('Closure'))->andReturnUsing(function ($c) {
                $c();
            });

        $this->call('GET', 'admin/users/foo/delete');
        $this->assertRedirectedTo('users');
    }

    /**
     * Test GET /admin/users/(:any)/delete when trying to delete own
     * account.
     *
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testGetDeleteActionWhenDeletingOwnAccount()
    {
        $builder = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $user    = m::mock('\Antares\Model\User');
        $auth    = (object) [
            'id' => 'foobar',
        ];

        $builder->shouldReceive('findOrFail')->once()->with('foobar')->andReturn($user);
        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn('foobar');

        Foundation::shouldReceive('make')->once()->with('antares.user')->andReturn($builder);
        Auth::shouldReceive('user')->once()->andReturn($auth);

        $this->call('GET', 'admin/users/foobar/delete');
    }

    /**
     * Test GET /admin/users/(:any)/delete when database error.
     *
     * @test
     */
    public function testGetDeleteActionGivenDatabaseError()
    {
        $builder = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $user    = m::mock('\Antares\Model\User');
        $auth    = (object) [
            'id' => 'foobar',
        ];

        $builder->shouldReceive('findOrFail')->once()->with('foo')->andReturn($user);
        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn('foo')
            ->shouldReceive('delete')->once()->andThrow('\Exception');

        Foundation::shouldReceive('make')->once()
            ->with('antares.user')->andReturn($builder);
        Foundation::shouldReceive('handles')->once()
            ->with('antares::users', [])->andReturn('users');
        Messages::shouldReceive('add')->once()
            ->with('error', m::any())->andReturnNull();
        Auth::shouldReceive('user')->once()->andReturn($auth);
        DB::shouldReceive('transaction')->once()
            ->with(m::type('Closure'))->andReturnUsing(function ($c) {
                $c();
            });

        $this->call('GET', 'admin/users/foo/delete');
        $this->assertRedirectedTo('users');
    }
}
