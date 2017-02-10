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
 namespace Antares\Html\Table\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Antares\Html\Table\Column;

class ColumnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Html\Table\Column::getVakye() method.
     */
    public function testGetValueMethod()
    {
        $stub = new Column([
            'value' => function ($row) {
                return '<strong>';
            },
        ]);

        $row = new Fluent();

        $this->assertEquals('<strong>', $stub->getValue($row));
        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub);
    }

    /**
     * Test Antares\Html\Table\Column::getVakye() method with escape
     * string.
     */
    public function testGetValueMethodWithEscapeString()
    {
        $stub = new Column([
            'value' => function ($row) {
                return '<strong>';
            },
            'escape' => true,
        ]);

        $row = new Fluent();

        $this->assertEquals('&lt;strong&gt;', $stub->getValue($row));
        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub);
    }
}
