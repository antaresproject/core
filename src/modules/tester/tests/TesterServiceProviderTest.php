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

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\Tester\TesterServiceProvider as Stub;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class TesterServiceProviderTest extends ApplicationTestCase
{

    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $this->disableMiddlewareForAllTests();
    }

    /**
     * Test checks whether regster method binds valid elements
     * 
     * @test
     */
    public function testRegisterMethod()
    {

        $app           = $this->app;
        $app['events'] = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app['files']  = m::mock('\Illuminate\Filesystem\Filesystem');
        $stub          = new Stub($app);
        $this->assertNull($stub->register());
        $this->assertInstanceOf('\Antares\Tester\Factory', app('antares.tester'));
    }

    /**
     * Test TesterServiceProvider::bootExtensionComponents() method.
     *
     * @test
     */
    public function testBootExtensionComponents()
    {

        $app           = $this->app;
        $app['events'] = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app['files']  = $files         = m::mock('\Illuminate\Filesystem\Filesystem');
        $files->shouldReceive('isDirectory')->with(m::type('string'))->andReturn(false);
        $stub          = new Stub($app);
        $stub->register();
        $this->assertNull($stub->bootExtensionComponents());
    }

}
