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

namespace Antares\Extension\TestCase;

use Mockery as m;
use Illuminate\Support\ServiceProvider;
use Antares\Extension\ProviderRepository;

class ProviderRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Extension\ProviderRepository::services()
     * method.
     *
     * @test
     */
    public function testServicesMethodWhenEager()
    {
        $mock = m::mock('\Antares\Extension\TestCase\FooServiceProvider');
        $app  = m::mock('\Illuminate\Contracts\Foundation\Application');

        $app->shouldReceive('resolveProviderClass')->once()
                ->with('Antares\Extension\TestCase\FooServiceProvider')->andReturn($mock)
                ->shouldReceive('register')->once()->with($mock)->andReturn($mock);

        $mock->shouldReceive('isDeferred')->once()->andReturn(false);

        $stub = new ProviderRepository($app);
        $stub->provides([
            'Antares\Extension\TestCase\FooServiceProvider',
        ]);
    }

    /**
     * Test Antares\Extension\ProviderRepository::services()
     * method.
     *
     * @test
     */
    public function testServicesMethodWhenDeferred()
    {
        $mock = m::mock('\Antares\Extension\TestCase\FooServiceProvider');
        $app  = m::mock('\Antares\Contracts\Foundation\DeferrableServiceContainer', '\Illuminate\Contracts\Foundation\Application');

        $app->shouldReceive('resolveProviderClass')->once()
                ->with('Antares\Extension\TestCase\FooServiceProvider')->andReturn($mock)
                ->shouldReceive('getDeferredServices')->once()->andReturn(['events' => '\Illuminate\Events\EventsServiceProvider'])
                ->shouldReceive('setDeferredServices')->once()->andReturn([
            'events' => 'Illuminate\Events\EventsServiceProvider',
            'foo'    => 'Antares\Extension\TestCase\FooServiceProvider',
        ]);

        $mock->shouldReceive('isDeferred')->once()->andReturn(true)
                ->shouldReceive('provides')->once()->andReturn(['foo']);

        $stub = new ProviderRepository($app);
        $stub->provides([
            'Antares\Extension\TestCase\FooServiceProvider',
        ]);
    }

}

class FooServiceProvider extends ServiceProvider
{

    public function register()
    {
        
    }

}
