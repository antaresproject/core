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
use Antares\Widget\Handlers\Pane;

class PaneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test construct a Antares\Widget\Handlers\Pane.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $stub   = new Pane('foo', []);

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
                'attributes' => [],
                'title'      => '',
                'content'    => '',
                'html'       => '',
            ],
        ];

        $this->assertEquals($expected, $config->getValue($stub));
        $this->assertEquals('foo', $name->getValue($stub));
        $this->assertInstanceOf('\Antares\Support\Nesty', $nesty->getValue($stub));
        $this->assertEquals('pane', $type->getValue($stub));
    }

    /**
     * Test Antares\Widget\Handlers\Pane::add() method.
     *
     * @test
     */
    public function testAddMethod()
    {
        $stub = new Pane('foo', []);

        $expected = new Collection([
            'foo' => new Fluent([
                'attributes' => [],
                'title'      => '',
                'content'    => 'hello world',
                'html'       => '',
                'id'         => 'foo',
                'childs'     => [],
            ]),
            'foobar' => new Fluent([
                'attributes' => [],
                'title'      => 'hello world',
                'content'    => '',
                'html'       => '',
                'id'         => 'foobar',
                'childs'     => [],
            ]),
            'hello' => new Fluent([
                'attributes' => [],
                'title'      => 'hello world',
                'content'    => '',
                'html'       => '',
                'id'         => 'hello',
                'childs'     => [],
            ]),
        ]);

        $callback = function ($item) {
            $item->title('hello world');
        };

        $stub->add('foo', function ($item) {
            $item->content('hello world');
        });

        $stub->add('foobar', '>:foo', $callback);

        $stub->add('hello', '^:foo', $callback);

        $this->assertEquals($expected, $stub->items());
    }
}
