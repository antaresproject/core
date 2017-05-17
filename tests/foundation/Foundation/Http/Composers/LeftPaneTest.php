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

namespace Antares\Foundation\Http\Composers\TestCase;

use Antares\Foundation\Http\Composers\LeftPane;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class LeftPaneTest extends ApplicationTestCase
{

    /**
     * test constructing
     * @test
     */
    public function testConstructing()
    {
        $router = m::mock(\Illuminate\Routing\Router::class);
        $router->shouldReceive('current')->withNoArgs()->andReturnSelf()
                ->shouldReceive('parameters')->withNoArgs()->andReturn([]);

        $widgetManager = $this->app->make(\Antares\UI\WidgetManager::class);
        $this->assertInstanceOf('\Antares\Foundation\Http\Composers\LeftPane', new LeftPane($widgetManager, $router));
    }

    /**
     * Tests compose method
     * 
     * @test
     */
    public function testCompose()
    {

        $router = m::mock(\Illuminate\Routing\Router::class);
        $router->shouldReceive('current')->withNoArgs()->andReturnSelf()
                ->shouldReceive('parameters')->withNoArgs()->andReturn([]);

        $widgetManager = $this->app->make(\Antares\UI\WidgetManager::class);

        $stub = new LeftPane($widgetManager, $router);
        $this->assertFalse($stub->compose('pane.foo'));
    }

}
