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

namespace Antares\Foundation\Publisher\TestCase;

use Antares\Foundation\Publisher\PublisherManager;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class PublisherManagerTest extends ApplicationTestCase
{

    /**
     * Test Antares\Foundation\Publisher\PublisherManager::getDefaultDriver()
     * method.
     *
     * @test
     */
    public function testGetDefaultDriverMethod()
    {
        $app = $this->app;

        $app['session']                 = $session                        = m::mock('\Illuminate\Session\SessionInterface');
        $app['antares.publisher.ftp']   = $client                         = m::mock('\Antares\Support\Ftp\Client');
        $app['antares.platform.memory'] = $memory                         = m::mock('\Antares\Contracts\Memory\Provider');
        $app['antares.publisher.ftp']   = m::mock('\Antares\Contracts\Publisher\Uploader');

        $memory->shouldReceive('get')->once()->with('antares.publisher.driver', 'ftp')->andReturn('ftp');

        $stub = (new PublisherManager($app))->attach($memory);
        $ftp  = $stub->driver();

        $this->assertInstanceOf('\Antares\Contracts\Publisher\Uploader', $ftp);
    }

    /**
     * Test Antares\Foundation\Publisher\PublisherManager::execute() method.
     *
     * @test
     */
    public function testExecuteMethod()
    {
        $app = $this->app;

        $app['antares.messages']        = $messages                       = m::mock('\Antares\Contracts\Messages\MessageBag');
        $app['antares.publisher.ftp']   = $client                         = m::mock('\Antares\Contracts\Publisher\Uploader');
        $app['translator']              = $translator                     = m::mock('\Illuminate\Translation\Translator')->makePartial();
        $app['antares.platform.memory'] = $memory                         = m::mock('\Antares\Contracts\Memory\Provider');

        $memory->shouldReceive('get')->once()->with('antares.publisher.queue', [])->andReturn(['a', 'b'])
                ->shouldReceive('get')->times(2)->with('antares.publisher.driver', 'ftp')->andReturn('ftp')
                ->shouldReceive('put')->once()->with('antares.publisher.queue', ['b'])->andReturnNull();
        $messages->shouldReceive('add')->once()->with('success', m::any())->andReturnNull()
                ->shouldReceive('add')->once()->with('error', m::any())->andReturnNull();
        $translator->shouldReceive('trans')->andReturn('foo');
        $client->shouldReceive('upload')->with('a')->andReturnNull()
                ->shouldReceive('upload')->with('b')->andThrow('\Exception');

        $stub = (new PublisherManager($app))->attach($memory);

        $this->assertTrue($stub->execute());
    }

    /**
     * Test Antares\Foundation\Publisher\PublisherManager::queue() method.
     *
     * @test
     */
    public function testQueueMethod()
    {
        $app                            = $this->app;
        $app['antares.platform.memory'] = $memory                         = m::mock('\Antares\Contracts\Memory\Provider');

        $memory->shouldReceive('get')->once()->with('antares.publisher.queue', [])
                ->andReturn(['foo', 'foobar'])
                ->shouldReceive('put')->once()->with('antares.publisher.queue', m::any())
                ->andReturnNull();

        $stub = (new PublisherManager($app))->attach($memory);
        $this->assertTrue($stub->queue(['foo', 'bar']));
    }

    /**
     * Test Antares\Foundation\Publisher\PublisherManager::queued() method.
     *
     * @test
     */
    public function testQueuedMethod()
    {
        $app                            = $this->app;
        $app['antares.platform.memory'] = $memory                         = m::mock('\Antares\Contracts\Memory\Provider');

        $memory->shouldReceive('get')->once()->with('antares.publisher.queue', [])->andReturn('foo');

        $stub = (new PublisherManager($app))->attach($memory);
        $this->assertEquals('foo', $stub->queued());
    }

}
