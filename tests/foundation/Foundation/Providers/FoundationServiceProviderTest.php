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
 namespace Antares\Foundation\Providers\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Foundation\Providers\FoundationServiceProvider;

class FoundationServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    /**
     * Test event are registered.
     *
     * @test
     */
    public function testRegisterEventsOnAfter()
    {
        $app           = new Container();
        $app['events'] = $events = m::mock('\Illuminate\Contracts\Events\Dispatcher[fire]');
        $app['router'] = $router = m::mock('\Illuminate\Routing\Router');
        $events->shouldReceive('fire')->once()->with('antares.done')->andReturnNull();

        $router->shouldReceive('after')->once()->with(m::type('Closure'))
            ->andReturnUsing(function ($c) {
                    $c();
                });

        $foundation = new FoundationServiceProvider($app);
        $foundation->register();
    }
}
