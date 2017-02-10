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
 namespace Antares\Foundation\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Foundation\AdminMenuHandler;

class AdminMenuHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\AdminMenuHandler::handle()
     * method.
     *
     * @test
     */
    public function testCreatingMenu()
    {
        $app = new Container();
        $app['antares.platform.menu'] = $menu = m::mock('\Antares\Widget\Handlers\Menu');

        $stub = new AdminMenuHandler($app);
        $stub->handle();
    }
}
