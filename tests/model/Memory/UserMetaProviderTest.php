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

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Model\Memory\UserMetaProvider;

class UserMetaProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Application instance.
     *
     * @var Illuminate\Model\Memory\Application
     */
    private $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();
    }

    /**
     * Test Antares\Model\Memory\UserMetaRepository::initiate()
     * method.
     *
     * @test
     */
    public function testInitiateMethod()
    {
        $handler = m::mock('\Antares\Model\Memory\UserMetaRepository');

        $handler->shouldReceive('initiate')->once()->andReturn([])
                ->shouldReceive('finish')->once()->andReturn(true);

        $stub = new UserMetaProvider($handler);

        $refl  = new \ReflectionObject($stub);
        $items = $refl->getProperty('items');

        $items->setAccessible(true);

        $items->setValue($stub, [
            'foo/user-1'    => '',
            'foobar/user-1' => 'foo',
            'foo/user-2'    => ':to-be-deleted:',
        ]);

        $this->assertTrue($stub->finish());
    }

    /**
     * Test Antares\Model\Memory\UserMetaRepository::get() method.
     *
     * @test
     */
    public function testGetMethod()
    {
        $handler = m::mock('\Antares\Model\Memory\UserMetaRepository');

        $handler->shouldReceive('initiate')->once()->andReturn([])
                ->shouldReceive('retrieve')->once()->with('foo/user-1')->andReturn('foobar')
                ->shouldReceive('retrieve')->once()->with('foobar/user-1')->andReturnNull();

        $stub = new UserMetaProvider($handler);

        $this->assertEquals('foobar', $stub->get('foo.1'));
        $this->assertEquals(null, $stub->get('foobar.1'));
    }

    /**
     * Test Antares\Model\Memory\UserMetaRepository::forget()
     * method.
     *
     * @test
     */
    public function testForgetMethod()
    {
        $handler = m::mock('\Antares\Model\Memory\UserMetaRepository');

        $handler->shouldReceive('initiate')->once()->andReturn([]);

        $stub = new UserMetaProvider($handler);

        $refl  = new \ReflectionObject($stub);
        $items = $refl->getProperty('items');

        $items->setAccessible(true);

        $items->setValue($stub, [
            'foo/user-1'   => 'foobar',
            'hello/user-1' => 'foobar',
        ]);

        $this->assertEquals('foobar', $stub->get('foo.1'));
        $stub->forget('foo.1');
        $this->assertNull($stub->get('foo.1'));
    }

}
