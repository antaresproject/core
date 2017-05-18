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

namespace Antares\Authorization\TestCase;

use Antares\Testing\ApplicationTestCase;
use Antares\Authorization\Factory;
use Mockery as m;

class FactoryTest extends ApplicationTestCase
{

    /**
     * Test Antares\Authorization\Factory::make().
     *
     * @test
     */
    public function testMakeMethod()
    {
        $auth = m::mock('\Antares\Contracts\Auth\Guard');
        $stub = new Factory($auth);

        $this->assertInstanceOf('\Antares\Authorization\Authorization', $stub->make('mock-one'));

        $memory = m::mock('\Antares\Memory\Provider');
        $memory->shouldReceive('get')->twice()->andReturn([]);

        $this->assertInstanceOf('\Antares\Authorization\Authorization', $stub->make('mock-two', $memory));
    }

    /**
     * Test Antares\Authorization\Factory::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $auth = m::mock('\Antares\Contracts\Auth\Guard');
        $stub = new Factory($auth);

        $auth->shouldReceive('guest')->times(3)->andReturn(true);

        $stub->register(function ($acl) {
            $acl->addActions(['view blog', 'view forum', 'view news']);
            $acl->allow('guest', ['view blog']);
            $acl->deny('guest', 'view forum');
        });

        $acl = $stub->make(null);
        $this->assertInstanceOf('\Antares\Authorization\Authorization', $acl);
        $this->assertTrue($acl->can('view blog'));
        $this->assertFalse($acl->can('view forum'));
        $this->assertFalse($acl->can('view news'));
    }

    /**
     * Test Antares\Authorization\Factory magic methods.
     *
     * @test
     */
    public function testMagicMethods()
    {
        $auth = m::mock('\Antares\Contracts\Auth\Guard');
        $stub = new Factory($auth);

        $acl1 = $stub->make('mock-one');
        $acl2 = $stub->make('mock-two');

        $stub->addRoles(['admin', 'manager', 'moderator']);
        $stub->removeRoles(['moderator']);

        $this->assertTrue($acl1->hasRole('admin'));
        $this->assertTrue($acl2->hasRole('admin'));
        $this->assertTrue($acl1->hasRole('manager'));
        $this->assertTrue($acl2->hasRole('manager'));

        $stub->removeRole('manager');

        $this->assertTrue($acl1->hasRole('admin'));
        $this->assertTrue($acl2->hasRole('admin'));
        $this->assertFalse($acl1->hasRole('manager'));
        $this->assertFalse($acl2->hasRole('manager'));

        $this->assertTrue(is_array($stub->all()));
        $this->assertFalse([] === $stub->all());

        $stub->finish();

        $this->assertEquals([], $stub->all());
    }

    /**
     * Test Antares\Authorization\Factory::all() method.
     *
     * @test
     */
    public function testAllMethod()
    {
        $auth = m::mock('\Antares\Contracts\Auth\Guard');
        $stub = new Factory($auth);

        $mock1 = $stub->make('mock-one');
        $mock2 = $stub->make('mock-two');
        $mock3 = $stub->make('mock-three');

        $expect = ['mock-one', 'mock-two', 'mock-three'];
        $this->assertEquals($expect, array_keys($stub->all()));

        $this->assertEquals($mock1, $stub->get('mock-one'));
        $this->assertEquals($mock2, $stub->get('mock-two'));
        $this->assertEquals($mock3, $stub->get('mock-three'));
        $this->assertNull($stub->get('mock-four'));
    }

}
