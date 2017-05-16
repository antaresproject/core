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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Customfields\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Customfields\Memory\FormsProvider;

class FormsProviderTest extends \PHPUnit_Framework_TestCase
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
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        m::close();
    }

    /**
     * Test \Antares\Customfields\Memory\FormsRepository::initiate()
     * method.
     *
     * @test
     */
    public function testInitiateMethod()
    {
        $handler = m::mock('\Antares\Customfields\Memory\FormsRepository');

        $handler->shouldReceive('initiate')->once()->andReturn([])
                ->shouldReceive('finish')->once()->andReturn(true);

        $stub = new FormsProvider($handler);

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
     * Test Antares\Customfields\Memory\FormsRepository::get() method.
     *
     * @test
     */
    public function testGetMethod()
    {
        $handler = m::mock('\Antares\Customfields\Memory\FormsRepository');

        $handler->shouldReceive('initiate')->once()->andReturn([])
                ->shouldReceive('retrieve')->once()->with('foo.1')->andReturn('foobar')
                ->shouldReceive('retrieve')->once()->with('foobar.1')->andReturnNull();

        $stub = new FormsProvider($handler);

        $this->assertEquals('foobar', $stub->get('foo.1'));
        $this->assertEquals(null, $stub->get('foobar.1'));
    }

    /**
     * Test Antares\Customfields\Memory\FormsRepository::forget()
     * method.
     *
     * @test
     */
    public function testForgetMethod()
    {
        $handler = m::mock('\Antares\Customfields\Memory\FormsRepository');

        $handler->shouldReceive('initiate')->once()->andReturn([]);

        $stub = new FormsProvider($handler);

        $refl  = new \ReflectionObject($stub);
        $items = $refl->getProperty('items');

        $items->setAccessible(true);

        $items->setValue($stub, [
            'foo'   => 'foobar',
            'hello' => 'foobar',
        ]);

        $this->assertEquals('foobar', $stub->get('foo'));
        $stub->forget('foo');
        $this->assertNull($stub->get('foo'));
    }

}
