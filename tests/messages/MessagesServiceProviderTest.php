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
 namespace Antares\Messages\TestCase;

use Mockery as m;
use Antares\Messages\MessagesServiceProvider;

class MessagesServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Support\MessagesServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app     = m::mock('\Illuminate\Container\Container');
        $session = m::mock('\Illuminate\Session\Store');

        $app->shouldReceive('singleton')->once()->with('antares.messages', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($app) {
                    $c($app);
                })
            ->shouldReceive('offsetGet')->once()->with('session.store')->andReturn($session);

        $stub = new MessagesServiceProvider($app);
        $this->assertNull($stub->register());
    }

    /**
     * Test Antares\Support\MessagesServiceProvider::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $app = [
            'router'             => $router = m::mock('\Illuminate\Routing\Router'),
            'antares.messages' => $messages = m::mock('\Antares\Message\MessageBag'),
        ];

        $router->shouldReceive('after')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) {
                    $c();
                });
        $messages->shouldReceive('save')->once();

        $stub = new MessagesServiceProvider($app);
        $this->assertNull($stub->boot());
    }
}
