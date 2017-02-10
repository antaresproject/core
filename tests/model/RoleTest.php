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
use Antares\Model\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{

    use \Antares\Support\Traits\Testing\EloquentConnectionTrait;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        Role::setDefaultRoles(['admin' => 10, 'member' => 20]);
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
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

        $grammar->shouldReceive('compileSelect')->once()->andReturn('SELECT * FROM `roles` WHERE id=?');
        $connection->shouldReceive('select')->once()->with('SELECT * FROM `roles` WHERE id=?', [10], true)->andReturn(null);
        $processor->shouldReceive('processSelect')->once()->andReturn([]);

        $model->admin();
    }

    /**
     * Test Antares\Model\Role::member() method.
     *
     * @test
     */
    public function testMemberMethod()
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

        $grammar->shouldReceive('compileSelect')->once()->andReturn('SELECT * FROM `roles` WHERE id=?');
        $connection->shouldReceive('select')->once()->with('SELECT * FROM `roles` WHERE id=?', [20], true)->andReturn(null);
        $processor->shouldReceive('processSelect')->once()->andReturn([]);

        $model->member();
    }

}
