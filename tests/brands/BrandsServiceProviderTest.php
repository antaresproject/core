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

namespace Antares\Brands\TestCase;

use Antares\Extension\ExtensionServiceProvider;
use Mockery as m;
use Antares\Brands\BrandsServiceProvider;
use Antares\Testbench\TestCase;

class BrandsServiceProviderTest extends TestCase
{

    /**
     * Test Antares\Support\MessagesServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app           = $this->app;
        $app['events'] = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app['files']  = m::mock('\Illuminate\Filesystem\Filesystem');

        $extensionStub = new ExtensionServiceProvider($app);
        $extensionStub->register();

        $stub = new BrandsServiceProvider($app);
        $stub->register();
        $this->assertInstanceOf('\Antares\Brands\Model\Brands', $app['antares.brand']);
        $this->assertInstanceOf('\Antares\Brands\BrandsTeller', $app['antares.brands']);
    }

    /**
     * Test Antares\Notifier\NotifierServiceProvider::boot() method.
     *
     * @test
     */
    public function testThrowExceptionWhenBootMethodAndInvalidMock()
    {
//        $app               = $this->app;
//        $app['events']     = m::mock('\Illuminate\Contracts\Events\Dispatcher');
//        $app['files']      = m::mock('\Illuminate\Filesystem\Filesystem');
//        $translator        = m::mock('\Illuminate\Translation\Translator');
//        $translator->shouldReceive('addNamespace')->withAnyArgs()->andReturnSelf();
//        $app['translator'] = $translator;
//        $stub              = new BrandsServiceProvider($app);
//        $stub->register();
//        $stub->bootExtensionComponents();
    }

}
