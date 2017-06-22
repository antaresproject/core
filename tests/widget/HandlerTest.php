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

namespace Antares\Widget\TestCase;

use Antares\Testbench\ApplicationTestCase;
use Antares\Support\Fluent;
use Antares\UI\Handler;
use Closure;

class HandlerTest extends ApplicationTestCase
{

    /**
     * Test construct a Antares\Widget\Handler.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $stub   = new HandlerStub('foo', []);
        $refl   = new \ReflectionObject($stub);
        $config = $refl->getProperty('config');
        $name   = $refl->getProperty('name');
        $nesty  = $refl->getProperty('nesty');
        $type   = $refl->getProperty('type');

        $config->setAccessible(true);
        $name->setAccessible(true);
        $nesty->setAccessible(true);
        $type->setAccessible(true);

        $this->assertEquals([], $config->getValue($stub));
        $this->assertEquals('foo', $name->getValue($stub));
        $this->assertInstanceOf('\Antares\Support\Nesty', $nesty->getValue($stub));
        $this->assertEquals('stub', $type->getValue($stub));
        $this->assertInstanceOf('\Antares\Support\Collection', $stub->getIterator());
        $this->assertEquals(0, count($stub));
    }

    /**
     * Test Antares\Widget\Handler::items() method.
     *
     * @test
     */
    public function testItemsMethod()
    {
        $stub = new HandlerStub('foo', []);

        $this->assertInstanceOf('\Antares\Support\Collection', $stub->items());
        $this->assertNull($stub->is('foo'));

        $stub->add('foobar')->hello('world');
        $expected = new Fluent([
            'id'     => 'foobar',
            'hello'  => 'world',
            'childs' => [],
            'active' => false
        ]);

        $this->assertEquals($expected, $stub->is('foobar'));
    }

}

class HandlerStub extends Handler
{

    protected $type   = 'stub';
    protected $config = [];

    public function add($id, $location = 'parent', $callback = null)
    {
        $item = $this->nesty->add($id, $location ?: 'parent');

        if ($callback instanceof Closure) {
            call_user_func($callback, $item);
        }

        return $item;
    }

}
