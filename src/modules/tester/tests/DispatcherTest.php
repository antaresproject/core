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

use Antares\Tester\Dispatcher as Stub;
use Antares\Testing\TestCase;
use Mockery as m;

class DispatcherTest extends TestCase
{

    /**
     * Test Antares\Tester\Dispatcher::__construct() method.
     *
     * @test
     */
    public function testConstruct()
    {
        $router  = m::mock('\Illuminate\Routing\Router');
        $request = m::mock('\Illuminate\Http\Request');


        $this->assertInstanceOf('Antares\Tester\Dispatcher', new Stub($this->app, $router, $request));
    }

}
