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
 namespace Antares\Widget\Handlers\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Antares\Support\Collection;
use Antares\Widget\Handlers\Placeholder;

class PlaceholderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

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
                'value' => '',
            ],
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
            'foo' => new Fluent([
                'value'  => $callback,
                'id'     => 'foo',
                'childs' => [],
            ]),
            'foobar' => new Fluent([
                'value'  => $callback,
                'id'     => 'foobar',
                'childs' => [],
            ]),
            'hello' => new Fluent([
                'value'  => $callback,
                'id'     => 'hello',
                'childs' => [],
            ]),
        ]);

        $stub->add('foo', $callback);
        $stub->add('foobar', '>:foo', $callback);
        $stub->add('hello', '^:foo', $callback);

        $this->assertEquals($expected, $stub->items());
    }
}
