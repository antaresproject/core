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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Tests;

use Antares\Tester\Factory as Stub;
use Antares\Testing\TestCase;
use Mockery as m;

class FactoryTest extends TestCase
{

    /**
     * Test Antares\Tester\Factory::__construct() method.
     *
     * @test
     */
    public function testConstruct()
    {
        $dispatcher = m::mock('\Antares\Tester\Dispatcher');
        $response   = m::mock('\Illuminate\Http\Response');

        $this->assertInstanceOf('Antares\Tester\Factory', new Stub($dispatcher, $response));
    }

}
