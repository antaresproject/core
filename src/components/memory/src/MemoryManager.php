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

namespace Antares\Memory;

use Exception;
use Illuminate\Support\Arr;
use Antares\Support\Manager;
use Antares\Memory\Handlers\Cache;
use Antares\Memory\Handlers\Fluent;
use Illuminate\Support\Facades\Log;
use Antares\Memory\Handlers\Runtime;
use Antares\Memory\Handlers\Eloquent;
use Antares\Memory\Handlers\Session;
use Antares\Memory\Handlers\Registry;
use Antares\Memory\Handlers\Component;
use Antares\Contracts\Memory\Handler as HandlerContract;

class MemoryManager extends Manager
{

    /**
     * Configuration values.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Create Fluent driver.
     *
     * @param  string  $name
     *
     * @return \Antares\Contracts\Memory\Provider
     */
    protected function createFluentDriver($name)
    {
        $config = Arr::get($this->config, "fluent.{$name}", []);
        $cache  = $this->getCacheRepository($config);

        return $this->createProvider(new Fluent($name, $config, $this->app->make('db'), $cache));
    }

    /**
     * Create Eloquent driver.
     *
     * @param  string  $name
     *
     * @return \Antares\Contracts\Memory\Provider
     */
    protected function createEloquentDriver($name)
    {
        $config = Arr::get($this->config, "eloquent.{$name}", []);
        $cache  = $this->getCacheRepository($config);

        return $this->createProvider(new Eloquent($name, $config, $this->app, $cache));
    }

    /**
     * Create Session driver.
     * @param  string  $name
     * @return \Antares\Contracts\Memory\Provider
     */
    protected function createSessionDriver($name)
    {
        $config = Arr::get($this->config, "session.{$name}", []);
        return $this->createProvider(new Session($name, $config));
    }

    /**
     * Create Cache driver.
     *
     * @param  string  $name
     *
     * @return \Antares\Contracts\Memory\Provider
     */
    protected function createCacheDriver($name)
    {
        $config = Arr::get($this->config, "cache.{$name}", []);
        $cache  = $this->getCacheRepository($config);

        return $this->createProvider(new Cache($name, $config, $cache));
    }

    /**
     * Create Runtime driver.
     *
     * @param  string  $name
     *
     * @return \Antares\Contracts\Memory\Provider
     */
    protected function createRuntimeDriver($name)
    {
        $config = Arr::get($this->config, "runtime.{$name}", []);
        return $this->createProvider(new Runtime($name, $config));
    }

    /**
     * Create Registry driver.
     * @param  string  $name
     * @return \Antares\Contracts\Memory\Provider
     */
    protected function createRegistryDriver($name)
    {
        $config = Arr::get($this->config, "registry.{$name}", []);
        $cache  = $this->getCacheRepository($config);

        return $this->createProvider(Registry::getInstance($name, $config, $this->app, $cache));
    }

    /**
     * Create Component driver.
     * @param  string  $name
     * @return \Antares\Contracts\Memory\Provider
     */
    protected function createComponentDriver($name)
    {
        $config = Arr::get($this->config, "component.{$name}", []);
        $cache  = $this->getCacheRepository($config);
        return $this->createProvider(new Component($name, $config, $this->app, $cache));
    }

    /**
     * Create Primary driver.
     * @param  string  $name
     * @return \Antares\Contracts\Memory\Provider
     */
    protected function createPrimaryDriver($name)
    {
        $config = Arr::get($this->config, "primary", []);
        $cache  = $this->getCacheRepository($config);

        return $this->createProvider(new Handlers\Primary($name, $config, $this->app, $cache));
    }

    /**
     * Create a memory provider.
     *
     * @param  \Antares\Contracts\Memory\Handler  $handler
     *
     * @return \Antares\Contracts\Memory\Provider
     */
    protected function createProvider(HandlerContract $handler)
    {
        return new Provider($handler);
    }

    /**
     * Get the default driver.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return Arr::get($this->config, 'driver', 'fluent.default');
    }

    /**
     * Set the default driver.
     *
     * @param  string  $name
     *
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->config['driver'] = $name;
    }

    /**
     * Get configuration values.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set configuration.
     *
     * @param  array  $config
     *
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Make default driver or fallback to runtime.
     *
     * @param  string  $fallbackName
     *
     * @return \Antares\Contracts\Memory\Provider
     */
    public function makeOrFallback($fallbackName = 'antares')
    {
        $fallback = null;

        try {
            $fallback = $this->make();
        } catch (Exception $e) {
            Log::emergency($e);
            $fallback = $this->driver("runtime.{$fallbackName}");
        }

        return $fallback;
    }

    /**
     * Loop every instance and execute finish method (if available).
     *
     * @return void
     */
    public function finish()
    {

        foreach ($this->drivers as $name => $class) {
            $handler = $class->getHandler();
            if (isset($handler->terminatable) && $handler->terminatable == false) {
                unset($this->drivers[$name]);
                continue;
            }

            if ($class->get('terminate') !== true) {
                $class->finish();
            }
            unset($this->drivers[$name]);
        }
        $this->drivers = [];
    }

    /**
     * Loop every instance and execute forget cache (if available).
     *
     * @return void
     */
    public function forgetCache()
    {
        foreach ($this->drivers as $name => $class) {
            $class->getHandler()->forgetCache();
        }
    }

    /**
     * Get cache repository.
     *
     * @param  array  $config
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getCacheRepository(array $config)
    {

        $connection = Arr::get($config, 'connections.cache');
        return $this->app->make('cache')->driver($connection);
    }

}
