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

namespace Antares\Foundation\Bootstrap\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Foundation\Application;
use Antares\Foundation\Bootstrap\NotifyIfSafeMode;

class NotifyIfSafeModeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    private $app;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Application(__DIR__);

        Facade::clearResolvedInstances();
        Container::setInstance($this->app);
    }

    /**
     * Test Antares\Foundation\Bootstrap\NotifyIfSafeMode::bootstrap()
     * method.
     *
     * @test
     */
    public function testBootstrapMethod()
    {
        $app = $this->app;

        $app['antares.extension.mode'] = $mode                          = m::mock('\Antares\Contracts\Extension\SafeMode');
        $app['antares.messages']       = $messages                      = m::mock('\Antares\Contracts\Messages\MessageBag');
        $app['translator']             = $translator                    = m::mock('\Illuminate\Translation\Translator')->makePartial();

        $messages->shouldReceive('extend')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($messages) {
                    return $c($messages);
                })
                ->shouldReceive('add')->once()->with('info', m::type('String'))->andReturnNull();

        $mode->shouldReceive('check')->once()->andReturn(true);

        (new NotifyIfSafeMode())->bootstrap($app);
    }

}
