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

namespace Antares\Html\Form\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Antares\Html\Form\Field;

class FieldTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Html\Form\Field::getField() method.
     *
     * @test
     */
    public function testGetFieldMethod()
    {
        $stub = new Field([
            'field' => function ($row) {
                return 'foo';
            },
        ]);

        $row     = new Fluent();
        $control = new Fluent();

        $this->assertEquals('foo', $stub->getField($row, $control));
        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub);
    }

    /**
     * Test Antares\Html\Form\Field::getField() method
     * when given \Illuminate\Support\Facades\Renderable.
     *
     * @test
     */
    public function testGetFieldMethodWhenGivenRenderable()
    {
        $renderable = m::mock('\Illuminate\Contracts\Support\Renderable');

        $renderable->shouldReceive('render')->once()->andReturn('foo');

        $stub = new Field([
            'field' => function ($row) use ($renderable) {
                return $renderable;
            },
        ]);

        $row     = new Fluent();
        $control = new Fluent();

        $this->assertEquals('foo', $stub->getField($row, $control));
        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub);
    }

}
