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

use Illuminate\Support\Arr;

abstract class Handler
{

    /**
     * Memory name.
     *
     * @var string
     */
    protected $name;

    /**
     * Storage name.
     *
     * @var string
     */
    protected $storage;

    /**
     * Repository instance.
     *
     * @var object
     */
    protected $repository;

    /**
     * Cache instance.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Cache key.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * Memory configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Cached key value map with md5 checksum.
     *
     * @var array
     */
    protected $keyMap = [];

    /**
     * Setup a new memory handler.
     *
     * @param  string  $name
     * @param  array   $config
     */
    public function __construct($name, array $config)
    {
        $this->name     = $name;
        $this->config   = array_merge($this->config, $config);
        $this->cacheKey = "db-memory:{$this->storage}-{$this->name}";
    }

    /**
     * Add key with id and checksum.
     *
     * @param  string  $name
     * @param  array   $option
     *
     * @return void
     */
    protected function addKey($name, $option)
    {

        $option['checksum'] = $this->generateNewChecksum($option['value']);
        unset($option['value']);

        $this->keyMap = Arr::add($this->keyMap, $name, $option);
    }

    /**
     * Verify checksum.
     *
     * @param  string  $name
     * @param  string  $check
     *
     * @return bool
     */
    protected function check($name, $check = '')
    {
        return (Arr::get($this->keyMap, "{$name}.checksum") === $this->generateNewChecksum($check));
    }

    /**
     * Generate a checksum from given value.
     *
     * @param  mixed  $value
     *
     * @return string
     */
    protected function generateNewChecksum($value)
    {
        !is_string($value) && $value = (is_object($value) ? spl_object_hash($value) : serialize($value));

        return md5($value);
    }

    /**
     * Is given key a new content.
     *
     * @param  string  $name
     *
     * @return int
     */
    protected function getKeyId($name)
    {
        return Arr::get($this->keyMap, "{$name}.id");
    }

    /**
     * Get storage name.
     *
     * @return string
     */
    public function getStorageName()
    {
        return $this->storage;
    }

    /**
     * Get handler name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get if from content is new.
     *
     * @param  string  $name
     *
     * @return bool
     */
    protected function isNewKey($name)
    {
        return is_null($this->getKeyId($name));
    }

    /**
     * force forgetting cache each driver
     * 
     * @return mixed
     */
    public function forgetCache()
    {
        return !is_null($this->cache) ? $this->cache->forget($this->cacheKey) : true;
    }

}
