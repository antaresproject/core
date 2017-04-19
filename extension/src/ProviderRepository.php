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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Extension;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Antares\Contracts\Foundation\DeferrableServiceContainer;
use Exception;

class ProviderRepository
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * List of services.
     *
     * @var array
     */
    protected $services = [];

    /**
     * Construct a new finder.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Load available service providers.
     *
     * @param  array  $services
     *
     * @return void
     */
    public function provides(array $services)
    {
        foreach ($services as $provider) {
            try {
                $instance = $this->app->resolveProviderClass($provider);
            } catch (Exception $ex) {
                continue;
            }
            if ($instance->isDeferred() && $this->app instanceof DeferrableServiceContainer) {
                $this->registerDeferredServiceProvider($instance, $provider);
            } else {
                $this->registerEagerServiceProvider($instance);
            }

            $this->services[] = $provider;
        }
    }

    /**
     * Register deferred service provider.
     *
     * @param  \Illuminate\Support\ServiceProvider  $instance
     * @param  string  $provider
     *
     * @return void
     */
    protected function registerDeferredServiceProvider(ServiceProvider $instance, $provider)
    {
        $services = $this->app->getDeferredServices();

        foreach ($instance->provides() as $service) {
            $services[$service] = $provider;
        }

        $this->app->setDeferredServices($services);
    }

    /**
     * Register eager service provider.
     *
     * @param  \Illuminate\Support\ServiceProvider  $instance
     *
     * @return void
     */
    protected function registerEagerServiceProvider(ServiceProvider $instance)
    {
        $this->app->register($instance);
    }

}
