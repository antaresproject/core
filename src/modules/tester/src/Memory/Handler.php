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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Tester\Memory;

use Illuminate\Support\Arr;
use Antares\Memory\Handler as SupportHandler;
use Illuminate\Contracts\Container\Container;
use Antares\Contracts\Memory\Handler as HandlerContract;

class Handler extends SupportHandler implements HandlerContract
{

    /**
     * Storage name.
     *
     * @var string
     */
    protected $storage = 'eloquent';

    /**
     * Cached user meta.
     *
     * @var array
     */
    protected $forms = [];

    /**
     * Setup a new memory handler.
     *
     * @param  string  $name
     * @param  array  $config
     * @param  \Illuminate\Contracts\Container\Container  $repository
     */
    public function __construct($name, array $config, Container $repository)
    {
        $this->repository = $repository;

        parent::__construct($name, $config);
    }

    /**
     * Initiate the instance.
     *
     * @return array
     */
    public function initiate()
    {
        $items    = [];
        $memories = $this->cache instanceof Repository ? $this->getItemsFromCache() : $this->getItemsFromDatabase();
        foreach ($memories as $memory) {
            $key   = $memory->name;
            $items = Arr::add($items, $key, unserialize($memory->value) + ['id' => $memory->id]);
            $this->addKey($key, [
                'id'    => $memory->id,
                'value' => $memory->value,
            ]);
        }
        return $items;
    }

    /**
     * Save data to database.
     *
     * @param  array   $items
     *
     * @return bool
     */
    public function finish(array $items = [])
    {
        $changed = false;
        foreach ($items as $key => $value) {
            $isNew = $this->isNewKey($key);
            $value = serialize($value);
            if (!$this->check($key, $value)) {
                $changed = true;
                $this->save($key, $value, $isNew);
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
            $model->value = $value;

            $model->save();
        }
    }

    /**
     * Get items from database.
     * @return array
     */
    protected function getItemsFromDatabase()
    {
        return $this->resolver()->get();
    }

    /**
     * get handler model instance
     * @return Eloquent
     */
    protected function resolver()
    {
        $model = Arr::get($this->config, 'model', $this->name);

        return $this->repository->make($model)->newInstance();
    }

}
