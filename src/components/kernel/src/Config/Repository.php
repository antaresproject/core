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


namespace Antares\Config;

use ArrayAccess;
use Illuminate\Support\Arr;
use Antares\Config\Traits\LoadingTrait;
use Antares\Config\Traits\CascadingTrait;
use Antares\Contracts\Config\PackageRepository;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class Repository extends NamespacedItemResolver implements ArrayAccess, ConfigContract, PackageRepository
{

    use CascadingTrait,
        LoadingTrait;

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * All of the registered packages.
     *
     * @var array
     */
    protected $packages = [];

    /**
     * Create a new configuration repository.
     *
     * @param  \Antares\Config\LoaderInterface  $loader
     * @param  string  $environment
     */
    public function __construct(LoaderInterface $loader, $environment)
    {
        $this->setLoader($loader);

        $this->environment = $environment;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function has($key)
    {
        $default = microtime(true);

        return $this->get($key, $default) !== $default;
    }

    /**
     * Determine if a configuration group exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function hasGroup($key)
    {
        list($namespace, $group) = $this->parseKey($key);

        return $this->loader->exists($group, $namespace);
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @param  mixed   $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        list($namespace, $group, $item) = $this->parseKey($key);

        $collection = $this->getCollection($group, $namespace);

        $this->load($group, $namespace, $collection);

        if (empty($item)) {
            return Arr::get($this->items, $collection, $default);
        }

        return Arr::get($this->items[$collection], $item, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return void
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $configKey => $configValue) {
                $this->setSingleItem($configKey, $configValue);
            }
        } else {
            $this->setSingleItem($key, $value);
        }
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function prepend($key, $value)
    {
        $config = $this->get($key);

        $this->setSingleItem($key, array_unshift($config, $value));
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function push($key, $value)
    {
        $config = $this->get($key);

        $this->setSingleItem($key, array_push($config, $value));
    }

    /**
     * Set a given collections of configuration value from cache.
     *
     * @param  array  $items
     *
     * @return $this
     */
    public function setFromCache(array $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Set a given configuration value.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  bool    $load
     *
     * @return void
     */
    protected function setSingleItem($key, $value = null, $load = true)
    {
        list($namespace, $group, $item) = $this->parseKey($key);

        $collection = $this->getCollection($group, $namespace);

        if ($load) {
            $this->load($group, $namespace, $collection);
        }

        if (is_null($item)) {
            $this->items[$collection] = $value;
        } else {
            Arr::set($this->items[$collection], $item, $value);
        }
    }

    /**
     * Load the configuration group for the key.
     *
     * @param  string  $group
     * @param  string  $namespace
     * @param  string  $collection
     *
     * @return void
     */
    protected function load($group, $namespace, $collection)
    {
        $env = $this->environment;

        if (isset($this->items[$collection])) {
            return;
        }

        $items = $this->loader->load($env, $group, $namespace);

        if (isset($this->afterLoad[$namespace])) {
            $items = $this->callAfterLoad($namespace, $group, $items);
        }

        $this->items[$collection] = $items;
    }

    /**
     * Register a package for cascading configuration.
     *
     * @param  string  $package
     * @param  string  $hint
     * @param  string  $namespace
     *
     * @return void
     */
    public function package($package, $hint, $namespace = null)
    {
        $namespace = $this->getPackageNamespace($package, $namespace);

        $this->packages[] = $namespace;

        $this->addNamespace($namespace, $hint);

        $this->afterLoading($namespace, function (Repository $me, $group, $items) use ($package) {
            $env = $me->getEnvironment();

            $loader = $me->getLoader();

            return $loader->cascadePackage($env, $package, $group, $items);
        });
    }

    /**
     * Get the configuration namespace for a package.
     *
     * @param  string  $package
     * @param  string  $namespace
     *
     * @return string
     */
    protected function getPackageNamespace($package, $namespace)
    {
        if (is_null($namespace)) {
            list(, $namespace) = explode('/', $package);
        }

        return $namespace;
    }

    /**
     * Get the collection identifier.
     *
     * @param  string  $group
     * @param  string  $namespace
     *
     * @return string
     */
    protected function getCollection($group, $namespace = null)
    {
        $namespace = $namespace ? : '*';

        return $namespace . '::' . $group;
    }

    /**
     * Get all of the configuration items.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }

}
