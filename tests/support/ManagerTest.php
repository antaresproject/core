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

namespace Antares\Support\TestCase;

use Mockery as m;

class ManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Support\Manager::driver() method.
     *
     * @test
     */
    public function testDriverMethod()
    {
        $stub = new ManagerStub(m::mock('\Illuminate\Foundation\Application'));
        $stub->extend('awesome', function ($app, $name) {
            return new ManagerAwesomeFoobar($app, $name);
        });

        $output1 = $stub->make('foo.bar');
        $output2 = $stub->driver('foo.bar');
        $output3 = $stub->driver('foo');
        $output4 = $stub->driver('foobar.daylerees');
        $output5 = $stub->driver('awesome.taylor');

        $this->assertInstanceOf('\Antares\Support\TestCase\ManagerFoo', $output1);
        $this->assertEquals('bar', $output1->name);
        $this->assertEquals($output1, $output2);
        $this->assertEquals('default', $output3->name);
        $this->assertNotEquals($output2, $output3);
        $this->assertInstanceOf('\Antares\Support\TestCase\ManagerFoobar', $output4);
        $this->assertEquals('daylerees', $output4->name);
        $this->assertInstanceOf('\Antares\Support\TestCase\ManagerAwesomeFoobar', $output5);
        $this->assertEquals('taylor', $output5->name);
    }

    /**
     * Test Antares\Support\Manager::driver() throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testDriverMethodThrowsException()
    {
        with(new ManagerStub(m::mock('\Illuminate\Foundation\Application')))->driver('invalidDriver');
    }

    /**
     * Test Antares\Support\Manager::driver() throws exception when name
     * contain dotted.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testDriverMethodGivenNameWithDottedThrowsException()
    {
        with(new ManagerStub(m::mock('\Illuminate\Foundation\Application')))
                ->driver('foo.bar.hello');
    }

}

class ManagerFoo
{

    public $name = null;

    public function __construct($app, $name)
    {
        $this->name = $name;
    }

}

class ManagerFoobar
{

    public $name = null;

    public function __construct($app, $name)
    {
        $this->name = $name;
    }

}

class ManagerAwesomeFoobar
{

    public $name = null;

    public function __construct($app, $name)
    {
        $this->name = $name;
    }

}

class ManagerStub extends \Antares\Support\Manager
{

    public function createFooDriver($name)
    {
        return new ManagerFoo($this->app, $name);
    }

    public function createFoobarDriver($name)
    {
        return new ManagerFoobar($this->app, $name);
    }

    public function getDefaultDriver()
    {
        return 'foo';
    }

}
