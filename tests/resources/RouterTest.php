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
 namespace Antares\Resources\TestCase;

use Antares\Resources\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test constructing Antares\Resources\Router::route() method.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $stub = new Router('foo', 'FooController');

        $refl       = new \ReflectionObject($stub);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);

        $expected = [
            'name'    => 'Foo',
            'id'      => 'foo',
            'routes'  => [],
            'uses'    => 'FooController',
            'visible' => true,
        ];

        $this->assertEquals($expected, $attributes->getValue($stub));
        $this->assertEquals('FooController', $stub->uses());

        $stub->set('foo', 'foobar');

        $this->assertEquals('foobar', $stub->get('foo'));

        $stub->forget('foo');

        $this->assertNull($stub->get('foo'));
    }

    /**
     * Test Antares\Resources\Router visibility methods.
     *
     * @test
     */
    public function testVisibilityMethods()
    {
        $stub = new Router('foo', 'FooController');

        $this->assertTrue($stub->visible);

        $this->assertEquals($stub, $stub->hide());
        $this->assertFalse($stub->visible);

        $this->assertEquals($stub, $stub->show());
        $this->assertTrue($stub->visible);
    }

    /**
     * Test Antares\Resources\Router::visibility() method
     * throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testVisibilityMethodThrowsException()
    {
        with(new Router('foo', 'FooController'))->visibility('foo');
    }

    /**
     * Test Antares\Resources\Router routing methods.
     *
     * @test
     */
    public function testRoutingMethods()
    {
        $stub = new Router('foo', 'FooController');

        $this->assertEquals($stub, $stub->route('first', 'FirstController'));

        $stub->second         = 'SecondController';
        $stub['third']        = 'ThirdController';
        $stub['third.fourth'] = 'ForthController';

        $expected = [
            'first'        => 'FirstController',
            'second'       => 'SecondController',
            'third'        => 'ThirdController',
            'third.fourth' => 'ForthController',
        ];

        $this->assertEquals($expected, $stub->get('routes'));

        unset($stub['first']);

        $this->assertEquals('ForthController', $stub['third.fourth']);
        $this->assertFalse(isset($stub['first']));
    }

    /**
     * Test Antares\Resources\Router::route() method given reserved
     * name throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testRouteMethodGivenReservedNameThrowsException()
    {
        with(new Router('foo', 'FooController'))->route('visible', 'FirstController');
    }

    /**
     * Test Antares\Resources\Router::route() method given name with
     * "/" throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testRouteMethodGivenNameWithSlashesThrowsException()
    {
        with(new Router('foo', 'FooController'))->route('first/foo', 'FirstController');
    }

    /**
     * Test Antares\Resources\Router::__call() method
     * throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodCallThrowsException()
    {
        with(new Router('foo', 'FooController'))->uses('FoobarController');
    }
}
