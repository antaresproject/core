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
 namespace Antares\Foundation\TestCase;

use Antares\Foundation\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Antares\Foundation\Application::registerBaseServiceProviders()
     * method.
     *
     * @test
     */
    public function testRegisterBaseServiceProviders()
    {
        $app = new Application(__DIR__);

        $this->assertInstanceOf('\Illuminate\Events\Dispatcher', $app['events']);
        $this->assertInstanceOf('\Antares\Routing\Router', $app['router']);
    }

    public function testGettingDeferredServices()
    {
        $app = new Application(__DIR__);

        $this->assertEquals([], $app->getDeferredServices());
    }
}
