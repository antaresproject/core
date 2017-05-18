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
use Antares\Model\Role;
use Mockery as m;

class RoleTest extends ApplicationTestCase
{

    use EloquentConnectionTrait;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        Role::setDefaultRoles(['admin' => 10, 'member' => 20]);
    }

    /**
     * Test Antares\Model\Role::users() method.
     *
     * @test
     */
    public function testUsersMethod()
    {
        $model = new Role();

        $this->addMockConnection($model);

        $stub = $model->users();

        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsToMany', $stub);
        $this->assertInstanceOf('\Antares\Model\User', $stub->getQuery()->getModel());
    }

    /**
     * Test Antares\Model\Role::admin() method.
     *
     * @test
     */
    public function testAdminMethod()
    {
        $model = new Role();

        $resolver   = m::mock('Illuminate\Database\ConnectionResolverInterface');
        $model->setConnectionResolver($resolver);
        $resolver->shouldReceive('connection')
                ->andReturn($connection = m::mock('Illuminate\Database\Connection'));
        $model->getConnection()
                ->shouldReceive('getQueryGrammar')
                ->andReturn($grammar    = m::mock('Illuminate\Database\Query\Grammars\Grammar'));
        $model->getConnection()
                ->shouldReceive('getPostProcessor')
                ->andReturn($processor  = m::mock('Illuminate\Database\Query\Processors\Processor'));

        $grammar->shouldReceive('compileSelect')->once()->andReturn('SELECT * FROM `roles` WHERE name=? or name=?');
        $connection->shouldReceive('select')->once()->with('SELECT * FROM `roles` WHERE name=? or name=?', array(0 => 'super-administrator', 1 => 'administrator',), true)->andReturn(null)
                ->shouldReceive('getName')->andReturn('mysql');
        $processor->shouldReceive('processSelect')->once()->andReturn([new Role(['name' => 'admin'])]);

        $this->assertInstanceOf(Role::class, $model->admin());
    }

    /**
     * Test Antares\Model\Role::member() method.
     *
     * @test
     */
    public function testMemberMethod()
    {
        $model      = new Role();
        $resolver   = m::mock('Illuminate\Database\ConnectionResolverInterface');
        $model->setConnectionResolver($resolver);
        $resolver->shouldReceive('connection')
                ->andReturn($connection = m::mock('Illuminate\Database\Connection'));
        $model->getConnection()
                ->shouldReceive('getQueryGrammar')
                ->andReturn($grammar    = m::mock('Illuminate\Database\Query\Grammars\Grammar'));
        $model->getConnection()
                ->shouldReceive('getPostProcessor')
                ->andReturn($processor  = m::mock('Illuminate\Database\Query\Processors\Processor'));

        $grammar->shouldReceive('compileSelect')->once()->andReturn('SELECT * FROM `roles` WHERE id=?');
        $connection->shouldReceive('select')->once()->with('SELECT * FROM `roles` WHERE id=?', [20], true)->andReturn(null)
                ->shouldReceive('getName')->andReturn('mysql');
        $processor->shouldReceive('processSelect')->once()->andReturn([]);

        $model->member();
    }

}
