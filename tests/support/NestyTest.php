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

use Antares\Testbench\ApplicationTestCase;
use Antares\Support\Collection;
use Antares\Support\Fluent;
use Antares\Support\Nesty;

class NestyTest extends ApplicationTestCase
{

    /**
     * Stub instance.
     *
     * @var Antares\Support\Nesty
     */
    private $stub = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $this->stub = new Nesty([]);
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->stub);
    }

    /**
     * Test instanceof stub.
     *
     * @test
     */
    public function testInstanceOfNesty()
    {
        $this->assertInstanceOf('\Antares\Support\Nesty', $this->stub);

        $refl   = new \ReflectionObject($this->stub);
        $config = $refl->getProperty('config');
        $config->setAccessible(true);

        $this->assertEquals([], $config->getValue($this->stub));
    }

    /**
     * Get newly instantiated Antares\Support\Nesty::get() return empty
     * string.
     *
     * @test
     */
    public function testNewInstanceReturnEmptyArray()
    {
        $this->assertEquals(new Collection([]), with(new Nesty([]))->items());
    }

    /**
     * Test adding an item to Antares\Support\Nesty.
     *
     * @test
     */
    public function testAddMethod()
    {
        $foobar = new Fluent([
            'id'     => 'foobar',
            'childs' => [
                'hello-foobar' => new Fluent([
                    'id'     => 'hello-foobar',
                    'childs' => [],
                        ]),
            ],
        ]);
        $foo    = new Fluent([
            'id'     => 'foo',
            'childs' => [
                'bar'                => new Fluent([
                    'id'     => 'bar',
                    'childs' => [],
                        ]),
                'foobar'             => $foobar,
                'foo-bar'            => new Fluent([
                    'id'     => 'foo-bar',
                    'childs' => [],
                        ]),
                'hello-world-foobar' => new Fluent([
                    'id'     => 'hello-world-foobar',
                    'childs' => [],
                        ]),
            ],
        ]);

        $expected = [
            'antares' => new Fluent([
                'id'     => 'antares',
                'childs' => [],
                'active' => false,
                    ]),
            'hello'   => new Fluent([
                'id'     => 'hello',
                'childs' => [],
                'active' => false,
                    ]),
            'world'   => new Fluent([
                'id'     => 'world',
                'childs' => [],
                'active' => false,
                    ]),
            'foo'     => $foo,
            'antares' => new Fluent([
                'id'     => 'antares',
                'childs' => [],
                'active' => false,
                    ]),
        ];

        $this->stub->add('foo');
        $this->stub->add('hello', '<:foo');
        $this->stub->add('world', '>:hello');
        $this->stub->add('bar', '^:foo');
        $this->stub->add('foobar', '^:foo');
        $this->stub->add('foo-bar', '^:foo');
        $this->stub->add('hello-foobar', '^:foo.foobar');
        $this->stub->add('hello-world-foobar', '^:foo.dummy');
        $this->stub->add('antares', '<');
        $this->stub->add('antares', '#');

        $this->assertEquals(new Collection($expected), $this->stub->items());
        $this->assertEquals($expected, $this->stub->is(null));
        $this->assertEquals($foo, $this->stub->is('foo'));
        $this->assertEquals($foobar, $this->stub->is('foo.foobar'));
        $this->assertNull($this->stub->is('foobar'));

        $this->assertTrue($this->stub->has('foo'));
        $this->assertTrue($this->stub->has('foo.foobar'));
        $this->assertFalse($this->stub->has('foo.foo'));
        $this->assertFalse($this->stub->has('bar'));
    }

    /**
     * Test Antares\Support\Nesty::addBefore() method.
     */
    public function testAddBeforeMethod()
    {
        $stub = new Nesty([]);

        $expected = [
            'foobar' => new Fluent([
                'id'     => 'foobar',
                'childs' => [],
                    ]),
            'foo'    => new Fluent([
                'id'     => 'foo',
                'childs' => [],
                    ]),
        ];

        $stub->add('foo', '<:home');
        $stub->add('foobar', '<:foo');

        $this->assertEquals(new Collection($expected), $stub->items());
    }

    /**
     * Test Antares\Support\Nesty::addAfter() method.
     */
    public function testAddAfterMethod()
    {
        $stub = new Nesty([]);

        $expected = [
            'foobar' => new Fluent([
                'id'     => 'foobar',
                'childs' => [],
                    ]),
            'foo'    => new Fluent([
                'id'     => 'foo',
                'childs' => [],
                    ]),
        ];

        $stub->add('foobar', '>:home');
        $stub->add('foo', '>:foobar');

        $this->assertEquals(new Collection($expected), $stub->items());
    }

    /**
     * Test adding an item to Antares\Support\Nesty when decendant is not
     * presented.
     *
     * @test
     */
    public function testAddMethodWhenDecendantIsNotPresented()
    {
        $stub = new Nesty([]);

        $stub->add('foo', '^:home');
        $this->assertEquals(new Collection([]), $stub->items());
    }

}
