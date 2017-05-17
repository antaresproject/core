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


namespace Antares\Html\Memory;

use Antares\Contracts\Memory\Handler as HandlerContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Crypt;
use Antares\Memory\DatabaseHandler;
use Illuminate\Support\Arr;
use RuntimeException;

class Handler extends DatabaseHandler implements HandlerContract
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
     * use crypting
     * 
     * @var boolean
     */
    protected $crypt;

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

        $this->crypt = Arr::get($this->config, 'crypt', false);
    }

    /**
     * compiling data into hashed string
     * 
     * @param mixed | array $value
     * @return String
     */
    protected function compile($value)
    {
        if ($this->crypt) {
            return Crypt::encrypt($value);
        }
        return serialize($value);
    }

    /**
     * reverse engineering on compiled set of data
     * 
     * @param String $value
     * @return array
     */
    protected function reverse($value)
    {
        if ($this->crypt) {
            return Crypt::decrypt($value);
        }
        return unserialize($value);
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
        if (!isset($value['cid']) or ! isset($value['aid'])) {
            throw new RuntimeException('Invalid memory form params. Unable to save form configruation.');
        }
        $model = $this->resolver()->where(['name' => $key, 'component_id' => $value['cid'], 'action_id' => $value['aid']])->first();
        $data  = serialize($value);
        if (true === $isNew && is_null($model)) {
            $this->resolver()->create([
                'name'         => $key,
                'component_id' => $value['cid'],
                'action_id'    => $value['aid'],
                'value'        => $data,
            ]);
        } else {
            $model->value = serialize($value);
            $model->save();
        }
    }

    /**
     * get key map
     * 
     * @return array
     */
    public function getKeyMap()
    {
        return $this->keyMap;
    }

    /**
     * get single keymap element
     * 
     * @param String $name
     * @param mixed $default
     * @return mixed
     */
    public function getKeyMapElement($name, $default = [])
    {
        return isset($this->keyMap[$name]) ? $this->keyMap[$name] : $default;
    }

}
