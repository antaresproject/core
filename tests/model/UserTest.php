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

namespace Antares\Model\TestCase;

use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Antares\Testing\ApplicationTestCase;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Hash;
use Antares\Model\User;
use Mockery as m;

class UserTest extends ApplicationTestCase
{

    use EloquentConnectionTrait;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        Facade::clearResolvedInstances();
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
        $stub  = $model->roles();
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
        $model = User::query()->where(['id' => 1])->first();
        $this->addMockConnection($model);
        $this->assertNull($model->attachRole(1));
    }

    /**
     * Test Antares\Model\User::detachRole() method.
     *
     * @test
     */
    public function testDetachRoleMethod()
    {
        $model = User::query()->where(['id' => 1])->first();
        $this->addMockConnection($model);
        $model->attachRole(1);
        $this->assertNull($model->detachRole(1));
    }

    /**
     * Test Antares\Model\User::hasRoles() method.
     *
     * @test
     */
    public function testIsMethod()
    {
        $model        = User::query()->where(['id' => 1])->first();
        $memberRoleId = \Antares\Model\Role::query()->where('name', 'member')->first()->id;
        $model->detachRole($memberRoleId);
        $this->assertTrue($model->hasRoles('super-administrator'));
        $this->assertFalse($model->hasRoles('member'));

        $model->attachRole($memberRoleId);


        $this->assertTrue($model->hasRoles(['super-administrator', 'member']));
        $this->assertFalse($model->hasRoles(['admin', 'user']));
    }

    /**
     * Test Antares\Support\Auth::hasRoles() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsMethodWhenInvalidRolesIsReturned()
    {
        $model = User::query()->where(['id' => 1])->first();
        $this->assertFalse($model->hasRoles('admin'));
        $this->assertFalse($model->hasRoles('user'));

        $this->assertFalse($model->hasRoles(['admin', 'editor']));
        $this->assertFalse($model->hasRoles(['admin', 'user']));
    }

    /**
     * Test Antares\Model\User::isNot() method.
     *
     * @test
     */
    public function testIsNotMethod()
    {
        $model = User::query()->where(['id' => 1])->first();
        $this->assertTrue($model->isNot('user'));
        $this->assertFalse($model->isNot('super-administrator'));

        $this->assertTrue($model->isNot(['superadmin', 'user']));
        $memberRoleId = \Antares\Model\Role::query()->where('name', 'member')->first()->id;
        $model->attachRole($memberRoleId);
        $this->assertFalse($model->isNot(['super-administrator', 'member']));
        $model->detachRole($memberRoleId);
    }

    /**
     * Test Antares\Support\Auth::isNot() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsNotMethodWhenInvalidRolesIsReturned()
    {
        $model = User::query()->where(['id' => 1])->first();

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
        $model = User::query()->where(['id' => 1])->first();

        $this->assertTrue($model->isAny(['super-administrator', 'user']));
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
        $model = User::query()->where(['id' => 1])->first();

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
        $model = User::query()->where(['id' => 1])->first();

        $this->assertTrue($model->isNotAny(['admin', 'user']));
        $this->assertFalse($model->isNotAny(['user', 'super-administrator']));
        $this->assertFalse($model->isNotAny(['super-administrator', 'editor']));
    }

    /**
     * Test Antares\Support\Auth::isNotAny() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsNotAnyMethodWhenInvalidRolesIsReturned()
    {
        $model = User::query()->where(['id' => 1])->first();

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
        $model = User::query()->where(['id' => 1])->first();

        $roles   = \Antares\Model\Role::query()->whereIn('name', ['member', 'administrator'])->get();
        $roleIds = $roles->pluck('id');
        foreach ($roleIds as $roleId) {
            $model->attachRole($roleId);
        }
        $this->assertEquals(['super-administrator', 'administrator', 'member'], $model->getRoles()->toArray());
        $model->detachRole($roles->where('name', 'member')->first()->id);
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
        $stub = new User();
        $this->assertNull($stub->setRememberToken('foobar'));
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
        $stub            = new User();
        $stub->firstname = 'Administrator';
        $stub->fullname  = 'Administrator';
        $this->assertEquals('Administrator', trim($stub->getRecipientName()));
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
        $stub->status = 1;
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
