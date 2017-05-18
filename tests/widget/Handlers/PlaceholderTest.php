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

namespace Antares\Widget\Handlers\TestCase;

use Antares\Testbench\ApplicationTestCase;
use Antares\Widget\Handlers\Placeholder;
use Antares\Support\Collection;
use Antares\Support\Fluent;
use Mockery as m;

class PlaceholderTest extends ApplicationTestCase
{

    /**
     * Test construct a Antares\Widget\Drivers\Handlers\Placeholder.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $stub = new Placeholder('foo', []);

        $refl   = new \ReflectionObject($stub);
        $config = $refl->getProperty('config');
        $name   = $refl->getProperty('name');
        $nesty  = $refl->getProperty('nesty');
        $type   = $refl->getProperty('type');

        $config->setAccessible(true);
        $name->setAccessible(true);
        $nesty->setAccessible(true);
        $type->setAccessible(true);

        $expected = [
            'defaults' => [
                'value'   => '',
                'content' => ''
            ],
            'content'  => ''
        ];
        $this->assertEquals($expected, $config->getValue($stub));
        $this->assertEquals('foo', $name->getValue($stub));
        $this->assertInstanceOf('\Antares\Support\Nesty', $nesty->getValue($stub));
        $this->assertEquals('placeholder', $type->getValue($stub));
    }

    /**
     * Test Antares\Widget\Handlers\Placeholder::add() method.
     *
     * @test
     */
    public function testAddMethod()
    {
        $stub = new Placeholder('foo', []);

        $callback = function () {
            return 'hello world';
        };

        $expected = new Collection([
            'foo'    => new Fluent([
                'value'   => $callback,
                'content' => '',
                'id'      => 'foo',
                'childs'  => [],
                'active'  => false
                    ]),
            'foobar' => new Fluent([
                'value'   => $callback,
                'content' => '',
                'id'      => 'foobar',
                'childs'  => [],
                'active'  => false
                    ]),
            'hello'  => new Fluent([
                'value'   => $callback,
                'content' => '',
                'id'      => 'hello',
                'childs'  => [],
                'active'  => false
                    ]),
        ]);

        $stub->add('foo', $callback);
        $stub->add('foobar', '>:foo', $callback);
        $stub->add('hello', '^:foo', $callback);
        $this->assertEquals($expected, $stub->items());
    }

}
