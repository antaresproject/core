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

namespace Antares\Foundation\TestCase;

use Mockery as m;
use Antares\Foundation\Meta;

class MetaTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Foundation\Meta::set() method.
     *
     * @test
     * @group support
     */
    public function testSetMethod()
    {
        $stub = new Meta();
        $stub->set('title', 'Foo');
        $stub->set('foo.bar', 'Foobar');

        $expected = ['title' => 'Foo', 'foo' => ['bar' => 'Foobar']];
        $this->assertEquals($expected, $stub->all());
    }

}
