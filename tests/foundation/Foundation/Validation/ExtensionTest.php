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
 namespace Antares\Foundation\Tests\Validation;

use Mockery as m;
use Antares\Foundation\Validation\Extension;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Antares\Foundation\Validation\Extension.
     *
     * @test
     */
    public function testInstance()
    {
        $events  = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $factory = m::mock('\Illuminate\Contracts\Validation\Factory');

        $stub = new Extension($factory, $events);

        $this->assertInstanceOf('\Antares\Foundation\Validation\Extension', $stub);
        $this->assertInstanceOf('\Antares\Support\Validator', $stub);
    }
}
