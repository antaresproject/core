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

namespace Antares\Acl\Tests;

use Mockery as m;
use Antares\Acl\Migration;
use Illuminate\Routing\Route;
use Antares\Acl\Action;
use Antares\Acl\RoleActionList;

class MigrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Mockery
     */
    protected $container;

    /**
     * @var RoleActionList
     */
    protected $roleActionList;

    public function setUp()
    {
        parent::setUp();

        $this->container = m::mock('\Illuminate\Container\Container');

        $this->roleActionList = new RoleActionList();

        $this->roleActionList->add('admin', [
            new Action('index', 'List'),
            new Action('add', 'Add Item'),
        ]);

        $this->roleActionList->add('customer', [
            new Action('index', 'List'),
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @return Migration
     */
    protected function getMigrationClass()
    {
        return new Migration($this->container);
    }

    public function testUpMethods()
    {
        $component = m::mock('\Antares\Contracts\Memory\Provider')
            ->shouldReceive('finish')
            ->once()
            ->andReturnNull()
            ->getMock();

        $roles     = m::mock('\Antares\Authorization\Fluent')->makePartial();
        $actions   = m::mock('\Antares\Authorization\Fluent')->makePartial();

        $memoryManager = m::mock('\Antares\Memory\MemoryManager')
                ->shouldReceive('make')
                ->once()
                ->with('component')
                ->andReturn($component)
                ->getMock();

        $authorization = m::mock('\Antares\Authorization\Authorization')
                ->shouldReceive('attach')
                ->with($component)
                ->once()
                ->andReturnNull()
                ->shouldReceive('roles')
                ->once()
                ->andReturn($roles)
                ->shouldReceive('actions')
                ->once()
                ->andReturn($actions)
                ->shouldReceive('allow')
                ->times(2)
                ->with(m::type('String'), m::type('Array'))
                ->andReturnNull()
                ->getMock();

        $acl = m::mock('\Antares\Authorization\Factory')
                ->shouldReceive('make')
                ->with('some_component_name')
                ->once()
                ->andReturn($authorization)
                ->getMock();


        $this->container
                ->shouldReceive('make')
                ->with('antares.acl')
                ->once()
                ->andReturn($acl)
                ->shouldReceive('make')
                ->with('antares.memory')
                ->once()
                ->andReturn($memoryManager)
                ->getMock();

        $this->getMigrationClass()->up('some_component_name', $this->roleActionList);
    }

    public function testDownMethod()
    {
        $component = m::mock('\Antares\Contracts\Memory\Provider')
                ->shouldReceive('forget')
                ->once()
                ->with('acl_some_component_name')
                ->andReturnNull()
                ->shouldReceive('finish')
                ->once()
                ->andReturnNull()
                ->getMock();

        $memoryManager = m::mock('\Antares\Memory\MemoryManager')
                ->shouldReceive('make')
                ->with('component')
                ->andReturn($component)
                ->getMock();

        $this->container
                ->shouldReceive('make')
                ->with('antares.memory')
                ->once()
                ->andReturn($memoryManager)
                ->getMock();

        $this->getMigrationClass()->down('some_component_name');
    }

}
