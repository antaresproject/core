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

namespace Antares\View\TestCase\Theme;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\View\Theme\ThemeManager;

class ThemeManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    private $app;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app                = new Container();
        $this->app['request']     = $request                  = m::mock('\Illuminate\Http\Request');
        $this->app['events']      = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $this->app['files']       = m::mock('\Illuminate\Filesystem\Filesystem');
        $this->app['path.base']   = '/var/antares';
        $this->app['path.public'] = '/var/antares/public';

        $request->shouldReceive('root')->andReturn('http://localhost/');
    }

    /**
     * Test contructing Antares\View\Theme\ThemeManager.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $app  = $this->app;
        $stub = new ThemeManager($app);
        $this->assertInstanceOf('\Antares\View\Theme\Theme', $stub->driver());
    }

    /**
     * Test Antares\View\Theme\ThemeManager::detect() method.
     *
     * @test
     */
    public function testDetectMethod()
    {
        $app                         = $this->app;
        $app['antares.theme.finder'] = $finder                      = m::mock('\Antares\View\Theme\Finder');

        $finder->shouldReceive('detect')->once()->andReturn('foo');

        $stub = new ThemeManager($app);
        $this->assertEquals('foo', $stub->detect());
    }

}
