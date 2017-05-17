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

use Antares\Support\Expression;

class ExpressionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test constructing Antares\Support\Expression.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $expected = "foobar";
        $actual   = new Expression($expected);

        $this->assertInstanceOf('\Antares\Support\Expression', $actual);
        $this->assertEquals($expected, $actual);
        $this->assertEquals($expected, $actual->get());
    }
}
