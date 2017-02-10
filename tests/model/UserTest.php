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
 namespace Antares\Model\TestCase;

use Mockery as m;
use Antares\Model\User;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Facade;
use Antares\Support\Traits\Testing\EloquentConnectionTrait;

class UserTest extends \PHPUnit_Framework_TestCase
{
    use EloquentConnectionTrait;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(new Container());
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Model\User::roles() method.
     *
     * @test
     */
    public function testRolesMethod()
    {
        $model = new User();

        $this->addMockConnection($model);

        $stub = $model->roles();

        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsToMany', $stub);
        $this->assertInstanceOf('\Antares\Model\Role', $stub->getQuery()->getModel());
    }

    /**
     * Test Antares\Model\User::attachRole() method.
     *
     * @test
     */
    public function testAttachRoleMethod()
    {
        $model        = m::mock('\Antares\Model\User[roles]');
        $relationship = m::mock('\Illuminate\Database\Eloquent\Relations\BelongsToMany')->makePartial();

        $model->shouldReceive('roles')->once()->andReturn($relationship);
        $relationship->shouldReceive('sync')->once()->with([2], false)->andReturnNull();

        $model->attachRole(2);
    }

    /**
     * Test Antares\Model\User::detachRole() method.
     *
     * @test
     */
    public function testDetachRoleMethod()
    {
        $model        = m::mock('\Antares\Model\User[roles]');
        $relationship = m::mock('\Illuminate\Database\Eloquent\Relations\BelongsToMany')->makePartial();

        $model->shouldReceive('roles')->once()->andReturn($relationship);
        $relationship->shouldReceive('detach')->once()->with([2])->andReturnNull();

        $model->detachRole(2);
    }

    /**
     * Test Antares\Model\User::is() method.
     *
     * @test
     */
    public function testIsMethod()
    {
        $model = m::mock('\Antares\Model\User[getRoles]');

        $model->shouldReceive('getRoles')->times(4)->andReturn(['admin', 'editor']);

        $this->assertTrue($model->is('admin'));
        $this->assertFalse($model->is('user'));

        $this->assertTrue($model->is(['admin', 'editor']));
        $this->assertFalse($model->is(['admin', 'user']));
    }

    /**
     * Test Antares\Support\Auth::is() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsMethodWhenInvalidRolesIsReturned()
    {
        $model = m::mock('\Antares\Model\User[getRoles]');

        $model->shouldReceive('getRoles')->times(4)->andReturn('foo');

        $this->assertFalse($model->is('admin'));
        $this->assertFalse($model->is('user'));

        $this->assertFalse($model->is(['admin', 'editor']));
        $this->assertFalse($model->is(['admin', 'user']));
    }

    /**
     * Test Antares\Model\User::isNot() method.
     *
     * @test
     */
    public function testIsNotMethod()
    {
        $model = m::mock('\Antares\Model\User[getRoles]');

        $model->shouldReceive('getRoles')->times(4)->andReturn(['admin', 'editor']);

        $this->assertTrue($model->isNot('user'));
        $this->assertFalse($model->isNot('admin'));

        $this->assertTrue($model->isNot(['superadmin', 'user']));
        $this->assertFalse($model->isNot(['admin', 'editor']));
    }

    /**
     * Test Antares\Support\Auth::isNot() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsNotMethodWhenInvalidRolesIsReturned()
    {
        $model = m::mock('\Antares\Model\User[getRoles]');

        $model->shouldReceive('getRoles')->times(4)->andReturn('foo');

        $this->assertTrue($model->isNot('admin'));
        $this->assertTrue($model->isNot('user'));

        $this->assertTrue($model->isNot(['admin', 'editor']));
        $this->assertTrue($model->isNot(['admin', 'user']));
    }

    /**
     * Test Antares\Model\User::isAny() method.
     *
     * @test
     */
    public function testIsAnyMethod()
    {
        $model = m::mock('\Antares\Model\User[getRoles]');

        $model->shouldReceive('getRoles')->twice()->andReturn(['admin', 'editor']);

        $this->assertTrue($model->isAny(['admin', 'user']));
        $this->assertFalse($model->isAny(['superadmin', 'user']));
    }

    /**
     * Test Antares\Support\Auth::isAny() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsAnyMethodWhenInvalidRolesIsReturned()
    {
        $model = m::mock('\Antares\Model\User[getRoles]');

        $model->shouldReceive('getRoles')->twice()->andReturn('foo');

        $this->assertFalse($model->isAny(['admin', 'editor']));
        $this->assertFalse($model->isAny(['admin', 'user']));
    }

    /**
     * Test Antares\Model\User::isNotAny() method.
     *
     * @test
     */
    public function testIsNotAnyMethod()
    {
        $model = m::mock('\Antares\Model\User[getRoles]');

        $model->shouldReceive('getRoles')->times(3)->andReturn(['admin', 'editor']);

        $this->assertTrue($model->isNotAny(['administrator', 'user']));
        $this->assertFalse($model->isNotAny(['user', 'editor']));
        $this->assertFalse($model->isNotAny(['admin', 'editor']));
    }

    /**
     * Test Antares\Support\Auth::isNotAny() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsNotAnyMethodWhenInvalidRolesIsReturned()
    {
        $model = m::mock('\Antares\Model\User[getRoles]');

        $model->shouldReceive('getRoles')->twice()->andReturn('foo');

        $this->assertTrue($model->isNotAny(['admin', 'editor']));
        $this->assertTrue($model->isNotAny(['admin', 'user']));
    }

    /**
     * Test Antares\Model\User::getRoles() method.
     *
     * @test
     */
    public function testGetRolesMethod()
    {
        $model        = m::mock('\Antares\Model\User[roles]');
        $relationship = m::mock('\Illuminate\Database\Eloquent\Relations\BelongsToMany')->makePartial();

        $model->shouldReceive('roles')->once()->andReturn($relationship);
        $relationship->shouldReceive('lists')->once()->andReturn(['admin', 'editor']);

        $this->assertEquals(['admin', 'editor'], $model->getRoles());
    }

    /**
     * Test Antares\Model\User::scopeSearch() method.
     *
     * @test
     */
    public function testScopeSearchMethod()
    {
        $model = new User();
        $this->addMockConnection($model);

        $keyword = 'foo*';
        $search  = 'foo%';
        $roles   = ['admin'];

        $query = m::mock('\Illuminate\Database\Eloquent\Builder');
        $query->shouldReceive('with')->once()->with('roles')->andReturn($query)
            ->shouldReceive('whereNotNull')->once()->with('users.id')->andReturn($query)
            ->shouldReceive('whereHas')->once()->with('roles', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($query) {
                    $c($query);
                })
            ->shouldReceive('whereIn')->once()->with('roles.id', $roles)->andReturn(null)
            ->shouldReceive('orWhere')->once()->with('email', 'LIKE', $search)->andReturn($query)
            ->shouldReceive('orWhere')->once()->with('fullname', 'LIKE', $search)->andReturn(null)
            ->shouldReceive('where')->once()->with(m::type('Closure'))->andReturnUsing(function ($q) use ($query, $keyword) {
                $q($query);
            });

        $this->assertEquals($query, $model->scopeSearch($query, $keyword, $roles));
    }

    /**
     * Test Antares\Model\User::getAuthIdentifier() method.
     *
     * @test
     */
    public function testGetAuthIdentifierMethod()
    {
        $stub     = new User();
        $stub->id = 5;

        $this->assertEquals(5, $stub->getAuthIdentifier());
    }

    /**
     * Test Antares\Model\User::getAuthPassword() method.
     *
     * @test
     */
    public function testGetAuthPasswordMethod()
    {
        Hash::swap($hash = m::mock('\Illuminate\Hashing\HasherInterface'));

        $hash->shouldReceive('make')->once()->with('foo')->andReturn('foobar');

        $stub           = new User();
        $stub->password = 'foo';

        $this->assertEquals('foobar', $stub->getAuthPassword());
    }

    /**
     * Test Antares\Model\User::getRememberToken() method.
     *
     * @test
     */
    public function testGetRememberTokenMethod()
    {
        $stub                 = new User();
        $stub->remember_token = 'foobar';

        $this->assertEquals('foobar', $stub->getRememberToken());
    }

    /**
     * Test Antares\Model\User::setRememberToken() method.
     *
     * @test
     */
    public function testSetRememberTokenMethod()
    {
        $stub = m::mock('\Antares\Model\User[setAttribute]');
        $stub->shouldReceive('setAttribute')->once()->with('remember_token', 'foobar')->andReturnNull();

        $stub->setRememberToken('foobar');
    }

    /**
     * Test Antares\Model\User::getRememberTokenName() method.
     *
     * @test
     */
    public function testGetRememberTokenNameMethod()
    {
        $stub = new User();
        $this->assertEquals('remember_token', $stub->getRememberTokenName());
    }

    /**
     * Test Antares\Model\User::getEmailForPasswordReset() method.
     *
     * @test
     */
    public function testGetEmailForPasswordResetMethod()
    {
        $stub        = new User();
        $stub->email = 'admin@antaresplatform.com';

        $this->assertEquals('admin@antaresplatform.com', $stub->getEmailForPasswordReset());
    }

    /**
     * Test Antares\Model\User::getRecipientEmail() method.
     *
     * @test
     */
    public function testGetRecipientEmailMethod()
    {
        $stub        = new User();
        $stub->email = 'admin@antaresplatform.com';

        $this->assertEquals('admin@antaresplatform.com', $stub->getRecipientEmail());
    }

    /**
     * Test Antares\Model\User::getRecipientName() method.
     *
     * @test
     */
    public function testGetRecipientNameMethod()
    {
        $stub           = new User();
        $stub->fullname = 'Administrator';

        $this->assertEquals('Administrator', $stub->getRecipientName());
    }

    /**
     * Test Antares\Model\User::activate() method.
     *
     * @test
     */
    public function testActivateMethod()
    {
        $stub         = new User();
        $stub->status = 0;

        $this->assertEquals($stub, $stub->activate());
    }

    /**
     * Test Antares\Model\User::deactivate() method.
     *
     * @test
     */
    public function testDeactivateMethod()
    {
        $stub         = new User();
        $stub->status = 1;

        $this->assertEquals($stub, $stub->deactivate());
    }

    /**
     * Test Antares\Model\User::suspend() method.
     *
     * @test
     */
    public function testSuspendMethod()
    {
        $stub         = new User();
        $stub->status = 1;

        $this->assertEquals($stub, $stub->suspend());
    }

    /**
     * Test Antares\Model\User::isActivated() method when account
     * is activated.
     *
     * @test
     */
    public function testIsActivatedMethodReturnTrue()
    {
        $stub         = new User();
        $stub->status = 0;

        $stub->activate();

        $this->assertTrue($stub->isActivated());
    }

    /**
     * Test Antares\Model\User::isActivated() method when account
     * is not activated.
     *
     * @test
     */
    public function testIsActivatedMethodReturnFalse()
    {
        $stub         = new User();
        $stub->status = 0;

        $this->assertFalse($stub->isActivated());
    }

    /**
     * Test Antares\Model\User::isSuspended() method when account
     * is suspended.
     *
     * @test
     */
    public function testIsSuspendedMethodReturnTrue()
    {
        $stub         = new User();
        $stub->status = 0;

        $this->assertFalse($stub->isSuspended());

        $stub->suspend();

        $this->assertTrue($stub->isSuspended());
    }

    public function testNotifyMethod()
    {
        $stub = m::mock('\Antares\Model\User')->makePartial();
        $stub->shouldAllowMockingProtectedMethods();

        $subject = 'foo';
        $view    = 'email.notification';
        $data    = ['foo' => 'bar'];

        $stub->shouldReceive('sendNotification')->once()
            ->with($stub, $subject, $view, $data)->andReturn(true);

        $this->assertTrue($stub->notify($subject, $view, $data));
    }

    /**
     * Test Antares\Model\User::isSuspended() method when account
     * is not suspended.
     *
     * @test
     */
    public function testIsSuspendedMethodReturnFalse()
    {
        $stub         = new User();
        $stub->status = 0;

        $this->assertFalse($stub->isActivated());
    }
}
