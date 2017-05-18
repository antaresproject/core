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

namespace Antares\Users\Http\Controllers\TestCase;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\Testing\ApplicationTestCase;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Mockery as m;

class UsersControllerTest extends ApplicationTestCase
{

    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        View::shouldReceive('share')->once()->with(m::type('string'), m::type('string'))->andReturnSelf()
                ->shouldReceive('addNamespace')->once()->with(m::type('string'), m::type('string'))->andReturnSelf()
                ->shouldReceive('make')->once()->withAnyArgs()->andReturnSelf()
                ->shouldReceive('with')->once()->with(m::type('array'))->andReturnSelf()
                ->shouldReceive('exists')->once()->withAnyArgs()->andReturn(true)
                ->shouldReceive('getFinder')->once()->withAnyArgs()->andReturn($finder = m::mock(\Illuminate\View\ViewFinderInterface::class))
                ->shouldReceive('render')->once()->withNoArgs()->andReturn('foo')
                ->shouldReceive('replaceNamespace')->withAnyArgs()->andReturnSelf();

        $this->disableMiddlewareForAllTests();
    }

    /**
     * Test GET /antares/users.
     *
     * @test
     */
    public function testGetIndexAction()
    {
        $datatable = m::mock(\Antares\Users\Http\Datatables\Users::class);
        $datatable->shouldReceive('render')->with("antares/foundation::users.index")->andReturn('foo');
        $this->app->instance(\Antares\Users\Http\Datatables\Users::class, $datatable);


        $this->call('GET', 'antares/users/index');
        $this->assertResponseOk();
    }

    /**
     * Test GET /antares/users/create.
     *
     * @test
     */
    public function testGetCreateAction()
    {

        $this->call('GET', 'antares/users/create');
        $this->assertResponseOk();
    }

    /**
     * Test GET /antares/users/(:any)/edit.
     *
     * @test
     */
    public function testGetEditAction()
    {


        $this->call('GET', 'antares/users/1/edit');
        $this->assertResponseOk();
    }

    /**
     * Test POST /antares/users.
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

        $user      = m::mock('\Antares\Model\User');
        $validator = m::mock('\Antares\Foundation\Validation\User');
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


        $this->call('POST', 'antares/users', $input);
        $this->assertRedirectedTo('antares/users/create');
    }

    /**
     * Test POST /antares/users when database error.
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


        $user      = m::mock('\Antares\Model\User');
        $validator = m::mock('\Antares\Foundation\Validation\User');
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


        $this->call('POST', 'antares/users', $input);
        $this->assertRedirectedTo('antares/users/create');
    }

    /**
     * Test POST /antares/users when validation error.
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

        $validator = m::mock('\Antares\Foundation\Validation\User');
        $validator->shouldReceive('on')->once()->with('create')->andReturn($validator)
                ->shouldReceive('with')->once()->with($input)->andReturn($validator)
                ->shouldReceive('getMessageBag')->once()->andReturn([])
                ->shouldReceive('fails')->once()->andReturn(true);

        $this->call('POST', 'antares/users', $input);
        $this->assertRedirectedTo('antares/users/create');
        $this->assertSessionHasErrors();
    }

    /**
     * Test PUT /antares/users/(:any).
     *
     * @test
     */
    public function testPutUpdateAction()
    {
        $input = [
            'id'       => '1',
            'email'    => 'email@antaresplatform.com',
            'fullname' => 'Administrator',
            'password' => '123456',
            'roles'    => [1],
        ];


        $builder   = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $user      = m::mock('\Antares\Model\User');
        $validator = m::mock('\Antares\Foundation\Validation\User');

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


        $this->call('PUT', 'antares/users/1', $input);
        $this->assertRedirectedTo('antares/users/1/edit');
    }

    /**
     * Test PUT /antares/users/(:any) when invalid user id is given.
     *
     * @test
     */
    public function testPutUpdateActionGivenInvalidUserId()
    {
        $input = [
            'id'        => '1',
            'email'     => 'email@antaresplatform.com',
            'firstname' => 'Antares',
            'lastname'  => 'Project',
            'password'  => '123456',
            'roles'     => [1],
        ];
        $this->call('PUT', 'antares/users/foobar', $input);
        $this->assertResponseStatus(500);
    }

    /**
     * Test PUT /antares/users/(:any) when database error.
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

        $validator = m::mock('\Antares\Foundation\Validation\User');

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


        DB::shouldReceive('transaction')->once()
                ->with(m::type('Closure'))->andReturnUsing(function ($c) {
            $c();
        });
        $this->call('PUT', 'antares/users/1', $input);
        $this->assertResponseStatus(500);
    }

    /**
     * Test PUT /antares/users/(:any) when validation error.
     *
     * @test
     */
    public function testPutUpdateActionGivenValidationError()
    {
        $input = [
            'id'        => '1',
            'email'     => '',
            'firstname' => '',
            'lastname'  => '',
            'password'  => '',
            'roles'     => [1],
        ];

        $validator = m::mock('\Antares\Foundation\Validation\User');
        $validator->shouldReceive('on')->once()->with('update')->andReturn($validator)
                ->shouldReceive('with')->once()->with($input)->andReturn($validator)
                ->shouldReceive('getMessageBag')->once()->andReturn([])
                ->shouldReceive('fails')->once()->andReturn(true);


        $this->call('PUT', 'antares/users/1', $input);
        $this->assertRedirectedTo('antares/users/1/edit');
    }

    /**
     * Test GET /antares/users/(:any)/delete.
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

        $this->app[\Illuminate\Contracts\Auth\Factory::class] = $auth                                                 = m::mock(\Illuminate\Contracts\Auth\Factory::class);
        $auth->shouldReceive('guest')->times(3)->andReturn(false);

        $user->shouldReceive('setAttribute')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('getAttribute')->with("id")->andReturn(10)
                ->shouldReceive('getAttribute')->with("roles")->andReturn(new \Antares\Support\Collection([
            [
                'id'        => 10,
                'name'      => 'administrator',
                'area'      => 'administrators',
                'full_name' => 'Administrator'
            ]
        ]));
        $user->id    = 10;
        $user->roles = new \Antares\Support\Collection([
            [
                'id'        => 10,
                'name'      => 'administrator',
                'area'      => 'administrators',
                'full_name' => 'Administrator'
            ]
        ]);

        $auth->shouldReceive('user')->once()->andReturn($user);
        DB::beginTransaction();
        $this->call('GET', 'antares/users/2/delete');
        DB::rollback();
        $this->assertRedirectedTo('users/index');
    }

    /**
     * Test GET /antares/users/(:any)/delete when trying to delete own
     * account.
     *
     * @test
     */
    public function testGetDeleteActionWhenDeletingOwnAccount()
    {
        $builder = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $user    = m::mock('\Antares\Model\User');
        $auth    = (object) [
                    'id' => 'foobar',
        ];

        $builder->shouldReceive('findOrFail')->once()->with('foo')->andReturn($user);
        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn('foo')->shouldReceive('delete')->once()->andReturnNull();

        $this->app[\Illuminate\Contracts\Auth\Factory::class] = $auth                                                 = m::mock(\Illuminate\Contracts\Auth\Factory::class);
        $auth->shouldReceive('guest')->times(3)->andReturn(false)
                ->shouldReceive('user')->andReturn($user);
        $role                                                 = [
            'id'        => 10,
            'name'      => 'administrator',
            'area'      => 'administrators',
            'full_name' => 'Administrator'
        ];
        $user->shouldReceive('setAttribute')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('getAttribute')->with("id")->andReturn(10)
                ->shouldReceive('getAttribute')->with("roles")->andReturn(new \Antares\Support\Collection([$role]));
        $user->id                                             = 10;
        $user->roles                                          = new \Antares\Support\Collection([$role]);

        $builder->shouldReceive('findOrFail')->once()->with('foobar')->andReturn($user);
        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn('foobar');
        DB::beginTransaction();
        $this->call('GET', 'antares/users/2/delete');
        DB::rollback();

        $this->assertRedirectedTo('users/index');
    }

    /**
     * Test GET /antares/users/(:any)/delete when database error.
     *
     * @test
     */
    public function testGetDeleteActionGivenDatabaseError()
    {
        $builder = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $user    = m::mock('\Antares\Model\User');

        $builder->shouldReceive('findOrFail')->once()->with('foo')->andReturn($user);
        $user->shouldReceive('getAttribute')->once()->with('id')->andReturn('foo')
                ->shouldReceive('delete')->once()->andThrow('\Exception');



        DB::shouldReceive('transaction')->once()
                ->with(m::type('Closure'))->andReturnUsing(function ($c) {
            $c();
        });
        $this->assertInstanceOf(ModelNotFoundException::class, $this->call('GET', 'antares/users/999/delete')->exception);
    }

}
