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


namespace Antares\Memory\Handlers;

use Illuminate\Support\Arr;
use Antares\Memory\Handler;
use Illuminate\Contracts\Cache\Repository;
use Antares\Contracts\Memory\Handler as HandlerContract;

class Cache extends Handler implements HandlerContract
{

    /**
     * Storage name.
     *
     * @var string
     */
    protected $storage = 'cache';

    /**
     * Setup a new memory handler.
     *
     * @param  string  $name
     * @param  array  $config
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     */
    public function __construct($name, array $config, Repository $cache)
    {
        $this->cache = $cache;

        $name = Arr::get($config, 'name', $name);

        parent::__construct($name, $config);
    }

    /**
     * Load the data from cache.
     *
     * @return array
     */
    public function initiate()
    {
        return $this->cache->get("antares.memory.{$this->name}", []);
    }

    /**
     * Save data to cache.
     *
     * @param  array  $items
     *
     * @return bool
     */
    public function finish(array $items = [])
    {
        $this->cache->forever("antares.memory.{$this->name}", $items);

        return true;
    }

}
