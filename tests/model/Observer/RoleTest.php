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

namespace Antares\Model\Observer\TestCase;

use Antares\Model\Observer\Role as RoleObserver;
use Mockery as m;

class RoleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Model\Observer\Role::creating()
     * method.
     *
     * @test
     */
    public function testCreatingMethod()
    {
        $acl   = m::mock('Antares\Contracts\Authorization\Factory');
        $model = m::mock('\Antares\Model\Role');

        $model->shouldReceive('getAttribute')->once()->with('name')->andReturn('foo');
        $acl->shouldReceive('addRole')->once()->with('foo')->andReturn(null);

        $stub = new RoleObserver($acl);
        $stub->creating($model);
    }

    /**
     * Test Antares\Model\Observer\Role::deleting()
     * method.
     *
     * @test
     */
    public function testDeletingMethod()
    {
        $acl   = m::mock('Antares\Contracts\Authorization\Factory');
        $model = m::mock('\Antares\Model\Role');

        $model->shouldReceive('getAttribute')->once()->with('name')->andReturn('foo');
        $acl->shouldReceive('removeRole')->once()->with('foo')->andReturn(null);

        $stub = new RoleObserver($acl);
        $stub->deleting($model);
    }

    /**
     * Test Antares\Model\Observer\Role::updating()
     * method.
     *
     * @test
     */
    public function testUpdatingMethod()
    {
        $acl   = m::mock('Antares\Contracts\Authorization\Factory');
        $model = m::mock('\Antares\Model\Role');

        $model->shouldReceive('getOriginal')->once()->with('name')->andReturn('foo')
                ->shouldReceive('getAttribute')->once()->with('name')->andReturn('foobar')
                ->shouldReceive('getDeletedAtColumn')->never()->andReturn('deleted_at')
                ->shouldReceive('isSoftDeleting')->once()->andReturn(false);
        $acl->shouldReceive('renameRole')->once()->with('foo', 'foobar')->andReturn(null);

        $stub = new RoleObserver($acl);
        $stub->updating($model);
    }

    /**
     * Test Antares\Model\Observer\Role::updating()
     * method for restoring.
     *
     * @test
     */
    public function testUpdatingMethodForRestoring()
    {
        $acl   = m::mock('Antares\Contracts\Authorization\Factory');
        $model = m::mock('\Antares\Model\Role');

        $model->shouldReceive('getOriginal')->once()->with('name')->andReturn('foo')
                ->shouldReceive('getAttribute')->once()->with('name')->andReturn('foobar')
                ->shouldReceive('getDeletedAtColumn')->once()->andReturn('deleted_at')
                ->shouldReceive('isSoftDeleting')->once()->andReturn(true)
                ->shouldReceive('getOriginal')->once()->with('deleted_at')->andReturn('0000-00-00 00:00:00')
                ->shouldReceive('getAttribute')->once()->with('deleted_at')->andReturn(null);
        $acl->shouldReceive('addRole')->once()->with('foobar')->andReturn(null);

        $stub = new RoleObserver($acl);
        $stub->updating($model);
    }

}
