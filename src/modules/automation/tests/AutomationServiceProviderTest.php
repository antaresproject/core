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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation\TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\Automation\AutomationServiceProvider;
use Antares\Testing\TestCase;
use Mockery as m;

class AutomationServiceProviderTest extends TestCase
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
     * Teardown the test environment.
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($_SERVER['StubBaseController@setupFilters']);
    }

    /**
     * Tests Antares\Automation\AutomationServiceProvider::register
     */
    public function testRegister()
    {
        $app           = $this->app;
        $app['events'] = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app['files']  = m::mock('\Illuminate\Filesystem\Filesystem');
        $stub          = new AutomationServiceProvider($app);
        $this->assertNull($stub->register());
    }

    /**
     * Tests Antares\Automation\AutomationServiceProvider::bootExtensionComponents
     */
    public function testBootExtensionComponents()
    {
        $app  = $this->app;
        $stub = new AutomationServiceProvider($app);
        $stub->register();
        $this->assertNull($stub->bootExtensionComponents());
    }

}
