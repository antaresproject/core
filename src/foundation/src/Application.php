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

namespace Antares\Foundation;

use Antares\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Foundation\Application as BaseApplication;
use Illuminate\Events\EventServiceProvider;
use Antares\Routing\RoutingServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Exception;

class Application extends BaseApplication implements ApplicationContract
{

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));
        $this->register(new RoutingServiceProvider($this));
    }

    /**
     * Get the path to the application configuration files.
     *
     * @return string
     */
    public function configPath($path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * Get the path to the database directory.
     *
     * @return string
     */
    public function databasePath($path = '')
    {
        return $this->databasePath ?: $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'database';
    }

    /**
     * Get the path to the cached extension.json file.
     *
     * @return string
     */
    public function getCachedExtensionServicesPath()
    {
        return $this->basePath() . '/bootstrap/cache/extension.php';
    }

    /**
     * Flush the container of all bindings and resolved instances.
     *
     * @return void
     */
    public function flush()
    {
        parent::flush();

        $this->hasBeenBootstrapped = false;
    }

    public function bootProvider(ServiceProvider $provider)
    {
        return parent::bootProvider($provider);
    }

    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        // Once the application has booted we will also fire some "booted" callbacks
        // for any listeners that need to do work after this initial booting gets
        // finished. This is useful when ordering the boot-up processes we run.
        $this->fireAppCallbacks($this->bootingCallbacks);

        array_walk($this->serviceProviders, function ($p) {
            $this->bootProvider($p);
        });

        $this->booted = true;

        $this->fireAppCallbacks($this->bootedCallbacks);
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param  string  $provider
     * @return \Illuminate\Support\ServiceProvider
     */
    public function resolveProviderClass($provider)
    {
        if (!class_exists($provider)) {
            throw new Exception(sprintf('Unable to find provider %s', $provider));
        }
        return new $provider($this);
    }

}
