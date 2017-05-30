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
 * @package    Widgets
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Widgets\Tests;

use Antares\Foundation\Http\Presenters\Extension as Extension2;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\Widgets\WidgetsServiceProvider as Stub;
use Antares\Widgets\Adapter\AfterValidateAdapter;
use Antares\Foundation\Validation\Extension;
use Antares\Widgets\Contracts\AfterValidate;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Antares\Testing\ApplicationTestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Mockery as m;

class WidgetsServiceProviderTest extends ApplicationTestCase
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
     * test checks whether regster method binds valid elements
     * 
     * @test
     */
    public function testRegisterMethod()
    {
        $app = $this->app;

        $presenter = m::mock(Extension2::class);
        $validator = m::mock(Extension::class);

        App::instance(Extension2::class, $presenter);
        App::instance(Extension::class, $validator);

        $app['config'] = $config        = m::mock(Repository::class);
        $app['events'] = m::mock(Dispatcher::class);
        $app['files']  = m::mock(Filesystem::class);

        $stub = new Stub($app);
        $stub->register();
        $this->assertInstanceOf(AfterValidate::class, app(AfterValidateAdapter::class));
    }

    /**
     * Test Antares\Widgets\WidgetsServiceProvider::bootExtensionComponents() method.
     *
     * @test
     */
    public function testBootExtensionComponents()
    {
        $stub = new Stub($this->app);
        $stub->register();
        $this->assertNull($stub->bootExtensionComponents());
    }

}
