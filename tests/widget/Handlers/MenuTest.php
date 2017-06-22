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
use Antares\UI\TemplateBase\Menu;
use Antares\Support\Collection;
use Antares\Support\Fluent;
use Mockery as m;

class MenuTest extends ApplicationTestCase
{

    /**
     * Test construct a Antares\Widget\Handlers\Menu.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $stub   = new Menu('foo', []);
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
                'icon'       => '',
                'link'       => '#',
                'title'      => '',
            ],
        ];

        $this->assertEquals($expected, $config->getValue($stub));
        $this->assertEquals('foo', $name->getValue($stub));
        $this->assertInstanceOf('\Antares\Support\Nesty', $nesty->getValue($stub));
        $this->assertEquals('menu', $type->getValue($stub));
    }

    /**
     * Test Antares\Widget\Handlers\Menu::add() method.
     *
     * @test
     */
    public function testAddMethod()
    {
        $stub = new Menu('foo', []);

        $expected = new Collection([
            'foo'    => new Fluent([
                'attributes' => [],
                'childs'     => [],
                'icon'       => '',
                'id'         => 'foo',
                'active'     => false,
                'link'       => '#',
                'title'      => 'hello world',
                    ]),
            'foobar' => new Fluent([
                'attributes' => [],
                'childs'     => [],
                'icon'       => '',
                'id'         => 'foobar',
                'active'     => false,
                'link'       => '#',
                'title'      => 'hello world 2',
                    ]),
        ]);

        $stub->add('foo', function ($item) {
            $item->title = 'hello world';
        });

        $stub->add('foobar', '>:foo', function ($item) {
            $item->title = 'hello world 2';
        });

        $this->assertEquals($expected, $stub->items());
    }

}
