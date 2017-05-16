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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Control\Memory;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Cache\Repository;
use Antares\Contracts\Memory\Handler as HandlerContract;
use Antares\Memory\DatabaseHandler;

class Middleware extends DatabaseHandler implements HandlerContract
{

    /**
     * does the class is termintable
     * 
     * @var boolean 
     */
    public $terminatable = false;

    /**
     * Storage name.
     *
     * @var string
     */
    protected $storage = 'eloquent';

    /**
     * Memory configuration.
     *
     * @var array
     */
    protected $config = ['cache' => false];

    /**
     * Setup a new memory handler.
     *
     * @param  string  $name
     * @param  array  $config
     * @param  \Illuminate\Contracts\Container\Container  $repository
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
     * Load the data from database.
     *
     * @return array
     */
    public function initiate()
    {
        $items    = [];
        $memories = $this->cache instanceof Repository ? $this->getItemsFromCache() : $this->getItemsFromDatabase();
        foreach ($memories as $memory) {
            $value                = $memory->value;
            $items[$memory->name] = unserialize($value);
            $this->addKey($memory->name, [
                'id'    => $memory->id,
                'value' => $value,
            ]);
        }
        return $items;
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
        if (!isset($this->keyMap[$name])) {
            return false;
        }
        return ($this->keyMap[$name]['checksum'] === $this->generateNewChecksum($check));
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

        $option['checksum']  = $this->generateNewChecksum($option['value']);
        unset($option['value']);
        $this->keyMap[$name] = $option;
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

            if (!$this->check($key, serialize($value))) {
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
     * Is given key a new content.
     *
     * @param  string  $name
     *
     * @return int
     */
    protected function getKeyId($name)
    {
        return isset($this->keyMap[$name]) ? $this->keyMap[$name]['id'] : null;
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

    /**
     * saving new object instance
     * 
     * @param String $key
     * @param mixed | array $value
     * @param boolean $isNew
     * @return \Illuminate\Database\Eloquent\Model | null
     */
    protected function save($key, $value, $isNew = false)
    {
        $cid   = $value['cid'];
        $aid   = $value['aid'];
        $model = $this->resolver()->where(['name' => $key, 'component_id' => $cid, 'action_id' => $cid])->first();
        $value = serialize($value);
        if (true === $isNew && is_null($model)) {
            $this->resolver()->create([
                'name'         => $key,
                'component_id' => $cid,
                'action_id'    => $aid,
                'value'        => $value,
            ]);
        } else {
            $model->value = $value;
            $model->save();
        }
    }

}
