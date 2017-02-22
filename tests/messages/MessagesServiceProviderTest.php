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

namespace Antares\Messages\TestCase;

use Antares\Messages\MessagesServiceProvider;
use Antares\Testing\ApplicationTestCase;
use Illuminate\Contracts\Http\Kernel;
use Mockery as m;

class MessagesServiceProviderTest extends ApplicationTestCase
{

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Support\MessagesServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $stub = new MessagesServiceProvider($this->app);
        $this->assertNull($stub->register());
    }

    /**
     * Test Antares\Support\MessagesServiceProvider::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $kernel = m::mock(Kernel::class);
        $kernel->shouldReceive('pushMiddleware')->andReturnSelf();
        $stub   = new MessagesServiceProvider($this->app);
        $this->assertNull($stub->boot($this->app['router'], $kernel));
    }

}
