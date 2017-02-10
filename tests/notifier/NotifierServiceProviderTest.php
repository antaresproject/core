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


namespace Antares\Notifier\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Notifier\NotifierServiceProvider;

class NotifierServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Notifier\NotifierServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app = m::mock('\Illuminate\Contracts\Container\Container');

        $app->shouldReceive('singleton')->once()->with('antares.mail', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($app) {
                    return $c($app);
                })
                ->shouldReceive('singleton')->once()->with('antares.notifier', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($app) {
                    return $c($app);
                });

        $stub = new NotifierServiceProvider($app);
        $stub->register();
    }

    /**
     * Test Antares\Notifier\NotifierServiceProvider::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $path = realpath(__DIR__.'/../');
        $app  = new Container();

        $app['path.base'] = '/var/laravel';
        $app['config']    = $config    = m::mock('\Antares\Contracts\Config\PackageRepository');

        $config->shouldReceive('package')->once()
                ->with('antares/notifier', "{$path}/resources/config", 'antares/notifier')->andReturnNull();
        $stub = new NotifierServiceProvider($app);

        $stub->boot();
    }

    /**
     * Test Antares\Notifier\NotifierServiceProvider::provides() method.
     *
     * @test
     */
    public function testProvidesMethod()
    {
        $app  = new Container();
        $stub = new NotifierServiceProvider($app);

        $this->assertEquals(['antares.mail', 'antares.notifier'], $stub->provides());
    }

    /**
     * Test Antares\Notifier\NotifierServiceProvider is deferred.
     *
     * @test
     */
    public function testServiceIsDeferred()
    {
        $app  = new Container();
        $stub = new NotifierServiceProvider($app);

        $this->assertTrue($stub->isDeferred());
    }

}
