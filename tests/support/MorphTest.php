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

class MorphTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test MorphStub::connect() return foo_connect().
     *
     * @test
     */
    public function testStubFooConnect()
    {
        $this->assertTrue(MorphStub::connect());
    }

    /**
     * Test MorphStub::invalid() throws an Exception.
     *
     * @expectedException \RuntimeException
     */
    public function testStubFooInvalidThrowsException()
    {
        MorphStub::invalid();
    }
}

function foo_connect()
{
    return true;
}

class MorphStub extends \Antares\Support\Morph
{
    public static $prefix = '\Antares\Support\TestCase\foo_';
}
