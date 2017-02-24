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
use Mockery as m;

class UserDashboardTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

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

        $widget->shouldReceive('make')->once()->with('pane.antares')->andReturn([]);

        $listener->shouldReceive('showDashboard')->once()->with(['panes' => []])->andReturn('show.dashboard');

        $this->assertEquals('show.dashboard', $stub->show($listener));
    }

}
