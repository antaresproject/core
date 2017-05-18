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
 namespace Antares\Database;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Cache\Repository;

class CacheDecorator
{
    /**
     * The key that should be used when caching the query.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * The number of minutes to cache the query.
     *
     * @var int
     */
    protected $cacheMinutes;

    /**
     * The Query Builder.
     *
     * @var \Illuminate\Database\Query\Builder
     */
    protected $query;

    /**
     * The cache repository implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $repository;

    public function __construct($query, Repository $repository)
    {
        $this->repository = $repository;
        $this->query      = $query;
    }

    /**
     * Indicate that the query results should be cached.
     *
     * @param  \DateTime|int  $minutes
     * @param  string  $key
     *
     * @return $this
     */
    public function remember($minutes, $key = null)
    {
        $this->cacheMinutes = $minutes;
        $this->cacheKey     = $key;

        return $this;
    }

    /**
     * Indicate that the query results should be cached forever.
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function rememberForever($key = null)
    {
        return $this->remember(-1, $key);
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array   $columns
     *
     * @return mixed|static
     */
    public function first($columns = ['*'])
    {
        $this->query->take(1);

        $results = $this->get($columns);

        if ($results instanceof Collection) {
            return $results->first();
        }

        return count($results) > 0 ? reset($results) : null;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     *
     * @return array|static[]
     */
    public function get($columns = ['*'])
    {
        if (! is_null($this->cacheMinutes)) {
            return $this->getCached($columns);
        }

        return $this->getFresh($columns);
    }

    /**
     * Execute the query as a cached "select" statement.
     *
     * @param  array  $columns
     *
     * @return array
     */
    public function getCached($columns = ['*'])
    {
                                list($key, $minutes) = $this->getCacheInfo();

        $cache = $this->getCache();

        $callback = $this->getCacheCallback($columns);

                                if ($minutes < 0) {
            return $cache->rememberForever($key, $callback);
        }

        return $cache->remember($key, $minutes, $callback);
    }

    /**
     * Execute the query as a fresh "select" statement.
     *
     * @param  array  $columns
     *
     * @return array|static[]
     */
    public function getFresh($columns = ['*'])
    {
        return $this->query->get($columns);
    }

    /**
     * Get a unique cache key for the complete query.
     *
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey ?: $this->generateCacheKey();
    }

    /**
     * Generate the unique cache key for the query.
     *
     * @return string
     */
    public function generateCacheKey()
    {
        $name = $this->getConnection()->getName();

        return md5($name.$this->toSql().serialize($this->getBindings()));
    }

    /**
     * Get the cache object with tags assigned, if applicable.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getCache()
    {
        return $this->repository;
    }

    /**
     * Get the Closure callback used when caching queries.
     *
     * @param  array  $columns
     *
     * @return \Closure
     */
    protected function getCacheCallback($columns)
    {
        return function () use ($columns) {
            return $this->getFresh($columns);
        };
    }

    /**
     * Get the cache key and cache minutes as an array.
     *
     * @return array
     */
    protected function getCacheInfo()
    {
        return [$this->getCacheKey(), $this->cacheMinutes];
    }

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->query, $method], $parameters);
    }
}
