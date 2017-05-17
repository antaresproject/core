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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
 namespace Antares\Testbench\Integrations\TestCase;

use Antares\Testbench\TestCase;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\AggregateServiceProvider;

class AggregateServiceProviderTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Antares\Testbench\Integrations\TestCase\ParentService',
        ];
    }

    /**
     * Test able to load aggregate service providers.
     *
     * @test
     */
    public function testServiceIsAvailable()
    {
        $this->assertTrue($this->app->bound('parent.loaded'));
        $this->assertTrue($this->app->bound('child.loaded'));
        $this->assertTrue($this->app->bound('child.deferred.loaded'));

        $this->assertTrue($this->app->make('parent.loaded'));
        $this->assertTrue($this->app->make('child.loaded'));
        $this->assertTrue($this->app->make('child.deferred.loaded'));
    }
}


class ParentService extends AggregateServiceProvider
{
    protected $providers = [
        'Antares\Testbench\Integrations\TestCase\ChildService',
        'Antares\Testbench\Integrations\TestCase\DeferredChildService',
    ];

    public function register()
    {
        parent::register();

        $this->app['parent.loaded'] = true;
    }
}

class ChildService extends ServiceProvider
{
    public function register()
    {
        $this->app['child.loaded'] = true;
    }
}

class DeferredChildService extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app['child.deferred.loaded'] = true;
    }
}