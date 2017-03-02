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

namespace Antares\Users\Processor\TestCase;

use Antares\Users\Processor\Account\ProfileDashboard;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class UserDashboardTest extends ApplicationTestCase
{

    /**
     * Test Antares\Users\Processor\UserDashboard::show()
     * method.
     *
     * @test
     */
    public function testShowMethod()
    {
        $listener = m::mock('\Antares\Contracts\Foundation\Listener\Account\ProfileDashboard');
        $widget   = m::mock('\Antares\Widget\WidgetManager');

        $stub = new ProfileDashboard($widget);



        $listener->shouldReceive('showDashboard')->once()->andReturn('show.dashboard');
        $this->app->instance(\Illuminate\Contracts\View\Factory::class, $factory = m::mock(\Illuminate\View\Factory::class));
        $factory->shouldReceive('make')->andReturnSelf()
                ->shouldReceive('render')->andReturn('rendered')
                ->shouldReceive('share')->andReturnSelf();

        $this->assertEquals('show.dashboard', $stub->show($listener));
    }

}
