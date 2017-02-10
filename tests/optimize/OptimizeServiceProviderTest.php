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
 namespace Antares\Optimize\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Optimize\OptimizeServiceProvider;

class OptimizeServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        m::close();
    }

    /**
     * Test Antares\Optimize\OptimizeServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app              = $this->app;
        $app['config']    = $config    = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['events']    = $events    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app['files']     = $files     = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['path.base'] = '/var/www/laravel';

        $files->shouldReceive('getRequire')->once()->andReturn([]);
        $events->shouldReceive('listen')->once()->with('artisan.start', m::type('Closure'))->andReturn(null);

        $stub = new OptimizeServiceProvider($app);

        $stub->register();

        $this->assertInstanceOf('\Antares\Optimize\OptimizeCommand', $app['antares.commands.optimize']);
    }

    /**
     * Test Antares\Optimize\OptimizeServiceProvider::provides() method.
     *
     * @test
     */
    public function testProvidesMethod()
    {
        $stub = new OptimizeServiceProvider($this->app);

        $expected = [
            'antares.commands.optimize',
            'antares.optimize',
        ];

        $this->assertEquals($expected, $stub->provides());
    }
}
