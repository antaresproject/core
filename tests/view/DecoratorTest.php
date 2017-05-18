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
 namespace Antares\View\TestCase;

use Antares\View\Decorator;

class DecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test add and using macros.
     *
     * @test
     */
    public function testAddAndUsingMacros()
    {
        $stub = new Decorator();

        $stub->macro('foo', function () {
            return 'foo';
        });

        $this->assertEquals('foo', $stub->foo());
    }

    /**
     * Test calling undefined macros throws an exception.
     *
     * @expectedException \BadMethodCallException
     */
    public function testCallingUndefinedMacrosThrowsException()
    {
        with(new Decorator())->foobar();
    }
}
