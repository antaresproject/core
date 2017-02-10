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
 namespace Antares\Foundation\Processor\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Foundation\Processor\AssetPublisher;

class AssetPublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Processor\AssetPublisher::executeAndRedirect()
     * method.
     *
     * @test
     */
    public function testExecuteAndRedirectMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\AssetPublishing');
        $publisher = m::mock('\Antares\Foundation\Publisher\PublisherManager');
        $session   = m::mock('\Illuminate\Session\Store');

        $stub = new AssetPublisher($publisher, $session);

        $publisher->shouldReceive('connected')->once()->andReturn(true)
            ->shouldReceive('execute')->once()->andReturn(true);
        $listener->shouldReceive('redirectToCurrentPublisher')->once()->andReturn('redirected');

        $this->assertEquals('redirected', $stub->executeAndRedirect($listener));
    }

    /**
     * Test Antares\Foundation\Processor\AssetPublisher::publish()
     * method.
     *
     * @test
     */
    public function testPublishMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\AssetPublishing');
        $publisher = m::mock('\Antares\Foundation\Publisher\PublisherManager');
        $session   = m::mock('\Illuminate\Session\Store');

        $input = $this->getInput();

        $stub = new AssetPublisher($publisher, $session);

        $publisher->shouldReceive('queued')->once()->andReturn(['laravel/framework'])
            ->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('connected')->once()->andReturn(true)
            ->shouldReceive('execute')->once()->andReturn(true);
        $session->shouldReceive('put')->once()->with('antares.ftp', $input)->andReturnNull();
        $listener->shouldReceive('publishingHasSucceed')->once()->andReturn('asset.published');

        $this->assertEquals('asset.published', $stub->publish($listener, $input));
    }

    /**
     * Test Antares\Foundation\Processor\AssetPublisher::publish()
     * method when connection failed.
     *
     * @test
     */
    public function testPublishMethodGivenConnectionFailed()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\AssetPublishing');
        $publisher = m::mock('\Antares\Foundation\Publisher\PublisherManager');
        $uploader  = m::mock('\Antares\Contracts\Publisher\Uploader');
        $session   = m::mock('\Illuminate\Session\Store');

        $input = $this->getInput();

        $stub = new AssetPublisher($publisher, $session);

        $publisher->shouldReceive('queued')->once()->andReturn(['laravel/framework'])
            ->shouldReceive('connect')->once()->andThrow('\Antares\Contracts\Publisher\ServerException');
        $session->shouldReceive('forget')->once()->with('antares.ftp')->andReturnNull();
        $listener->shouldReceive('publishingHasFailed')->once()->andReturn(['error' => 'failed']);

        $this->assertEquals(['error' => 'failed'], $stub->publish($listener, $input));
    }

    /**
     * Get request input.
     *
     * @return array
     */
    protected function getInput()
    {
        return [
            'host'     => 'localhost',
            'username' => 'foo',
            'password' => 'foobar',
            'ssl'      => false,
        ];
    }
}
