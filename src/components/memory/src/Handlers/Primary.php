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
use Antares\Memory\DefaultHandler;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\Container;

class Primary extends DefaultHandler
{

    /**
     * Storage name.
     *
     * @var string
     */
    protected $storage = 'primary';

    /**
     * Memory configuration.
     *
     * @var array
     */
    protected $config = [
        'cache' => false,
    ];

    /**
     * Setup a new memory handler.
     *
     * @param  string  $name
     * @param  array  $config
     * @param  \Illuminate\Contracts\Container\Container  $repository
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     */
    public function __construct($name, array $config, Container $repository, Repository $cache)
    {
        parent::__construct($name, $config);
        $this->repository = $repository;

        if (Arr::get($this->config, 'cache', false)) {
            $this->cache = $cache;
        }
    }

    /**
     * @inherit
     * @param array $items
     * @return boolean
     */
    public function finish(array $items = [])
    {
        $changed = false;
        if (!isset($items['terminate'])) {
            $items = array_dot($items);
            foreach ($items as $key => $value) {

                $isNew = $this->isNewKey($key);
                if (!$this->check($key, $value)) {
                    $changed = true;
                    $this->save($key, $value, $isNew);
                }
            }
        }
        if ($changed && $this->cache instanceof Repository) {
            $this->cache->forget($this->cacheKey);
        }

        return true;
    }

    /**
     * Create/insert data to database.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  bool    $isNew
     *
     * @return bool
     */
    protected function save($key, $value, $isNew = false)
    {
        $model = $this->resolver()->where('name', '=', $key)->first();

        if (true === $isNew && is_null($model)) {
            $this->resolver()->create([
                'name'  => $key,
                'value' => $value,
            ]);
        } else {
            if (is_null($model)) {
                $model = $this->resolver();
                $model->fill(['name' => $key]);
            }
            $model->value = $value;
            $model->save();
        }
    }

    /**
     * Get resolver instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function resolver()
    {
        $model = Arr::get($this->config, 'model', $this->name);
        return $this->repository->make($model)->newInstance();
    }

    /**
     * instant update
     * @param array $items
     */
    public function update(array &$items = [])
    {
        !is_null($this->cache) && $this->cache->forget($this->cacheKey);
        $items['terminate'] = $this->finish($items);
    }

    /**
     * deletes item from resolver
     * 
     * @param String $key
     * @return boolean
     */
    public function forceDelete($key)
    {
        !is_null($this->cache) && $this->cache->forget($this->cacheKey);
        $model = $this->resolver()->where('name', $key);
        if (!is_null($model)) {
            return $model->delete();
        }
        return false;
    }

}
