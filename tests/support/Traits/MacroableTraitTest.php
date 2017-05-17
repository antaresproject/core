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
 namespace Antares\Support\Traits\TestCase;

use Antares\Support\Traits\MacroableTrait;

class MacroableTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test \Antares\Support\Traits\MacroableTrait is executable.
     *
     * @test
     */
    public function testMacroIsExecutable()
    {
        $stub = new MacroableStub();

        $stub->macro('foo', function () {
            return 'foobar';
        });

        $this->assertEquals('foobar', $stub->foo());
    }

    /**
     * Test \Antares\Support\Traits\MacroableTrait throws an exception
     * when macro is not executable.
     *
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Method foo does not exist.
     */
    public function testMacroThrowsExceptionWhenMacroIsntExecutable()
    {
        with(new MacroableStub())->foo();
    }
}

class MacroableStub
{
    use MacroableTrait;
}
