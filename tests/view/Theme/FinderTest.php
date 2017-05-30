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
use Antares\View\Theme\Finder;
use Illuminate\Container\Container;

class FinderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    private $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();
    }

    /**
     * Test Antares\Theme\Finder::detect() method.
     *
     * @test
     */
    public function testDetectMethod()
    {
        $app                = $this->app;
        $app['path.public'] = '/var/antares/public/';
        $app['files']       = $file               = m::mock('\Illuminate\Filesystem\Filesystem');

        $file->shouldReceive('directories')->once()
                ->with('/var/antares/public/themes/')->andReturn([
                    '/var/antares/public/themes/a',
                    '/var/antares/public/themes/b',
                ])
                ->shouldReceive('exists')->once()
                ->with('/var/antares/public/themes/a/theme.json')->andReturn(true)
                ->shouldReceive('exists')->once()
                ->with('/var/antares/public/themes/b/theme.json')->andReturn(false)
                ->shouldReceive('get')->once()
                ->with('/var/antares/public/themes/a/theme.json')->andReturn('{"name": "foo"}');

        $stub   = new Finder($app);
        $themes = $stub->detect();

        $this->assertInstanceOf('\Antares\View\Theme\Manifest', $themes['a']);
        $this->assertEquals('/var/antares/public/themes/a', $themes['a']->path);
    }

}
