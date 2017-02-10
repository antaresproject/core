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
use Illuminate\Support\Fluent;
use Antares\Foundation\Processor\ResourceLoader;

class ResourceLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Processor\ResourceLoader::showAll()
     * method.
     *
     * @test
     */
    public function testShowAllMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\ResourceLoader');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Resource');
        $resources = m::mock('\Antares\Resources\Factory');

        $data = [
            'laravel' => new Fluent(['visible' => true, 'name' => 'Laravel']),
        ];

        $stub = new ResourceLoader($presenter, $resources);

        $resources->shouldReceive('all')->once()->andReturn($data);
        $presenter->shouldReceive('table')->once()->with(m::type('Array'))->andReturn('table');
        $listener->shouldReceive('showResourcesList')->once()
            ->with(m::type('Array'))->andReturn('show.all');

        $this->assertEquals('show.all', $stub->index($listener));
    }

    /**
     * Test Antares\Foundation\Processor\ResourceLoader::showAll()
     * method.
     *
     * @test
     */
    public function testRequestMethod()
    {
        $listener  = m::mock('\Antares\Contracts\Foundation\Listener\ResourceLoader');
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Resource');
        $resources = m::mock('\Antares\Resources\Factory');

        $data = [
            'laravel' => new Fluent(['visible' => true, 'name' => 'Laravel']),
        ];

        $stub = new ResourceLoader($presenter, $resources);

        $resources->shouldReceive('all')->once()->andReturn($data)
            ->shouldReceive('call')->once()->with('laravel', [])->andReturn('Laravel')
            ->shouldReceive('response')->once()->with('Laravel', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) {
                    return $c('Laravel');
                });
        $listener->shouldReceive('onRequestSucceed')->once()
            ->with(m::type('Array'))->andReturn('request.succeed');

        $this->assertEquals('request.succeed', $stub->show($listener, 'laravel'));
    }
}
