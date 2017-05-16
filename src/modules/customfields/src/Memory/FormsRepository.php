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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\Memory;

use Antares\Contracts\Memory\Handler as HandlerContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Antares\Memory\Handler;

class FormsRepository extends Handler implements HandlerContract
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
        $memories = $this->cache instanceof Repository ? $this->getItemsFromCache() : $this->retrieveAll();
        return $memories;
    }

    /**
     * Get value from database.
     * @param  string   $key
     * @return mixed
     */
    public function retrieve($key)
    {
        if (strpos($key, '.') !== FALSE) {
            list($name, $value) = explode('.', $key);

            if (empty($this->forms)) {
                $data        = $this->getModel()->where('name', '=', $name)->get();
                $this->forms = $this->processRetrievedData($data);
            }

            return Arr::get($this->forms, "{$name}.{$value}");
        }
    }

    /**
     * Add a finish event.
     * @param  array  $items
     * @return bool
     */
    public function finish(array $items = [])
    {
        foreach ($items as $item) {
            if (is_null(array_get($this->keyMap, $item))) {
                $explode = explode('.', $item);
                $this->saving($explode[0], $explode[1]);
            }
        }
        return true;
    }

    /**
     * Process retrieved data.
     *
     * @param  string|int  $userId
     * @param  \Illuminate\Support\Collection|array  $data
     *
     * @return void
     */
    protected function processRetrievedData($data = [])
    {
        $items = [];

        foreach ($data as $eloquent) {
            $value = $eloquent->name;


            foreach ($eloquent->group as $group) {
                $value         = implode('.', [$eloquent->name, $group->name]);
                $this->addKey($value, [
                    'id'    => $group->id,
                    'value' => $value,
                ]);
                $items[$value] = $value;
            }
        }
        return $items;
    }

    /**
     * Save user meta to memory.
     *
     * @param  mixed    $key
     * @param  mixed    $value
     *
     * @return void
     */
    protected function save($key, $value)
    {
        $isNew = $this->isNewKey($value);
        list($name, $value) = explode('.', $value);
        $this->saving($name, $value, $isNew);
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
        return Arr::get($this->keyMap, $name);
    }

    /**
     * Process saving the value to memory.
     *
     * @param  string  $name
     * @param  mixed   $userId
     * @param  mixed   $value
     * @param  bool    $isNew
     *
     * @return void
     */
    protected function saving($name, $value = null, $isNew = true)
    {
        DB::transaction(function() use ($name, $value, $isNew) {
            $category = $this->getModel()->search($name)->first();

            if (is_null($value) || $value === ':to-be-deleted:') {
                !is_null($category) && $category->delete();
                return;
            }

            if (true === $isNew && is_null($category)) {
                $category       = $this->getModel();
                $category->name = $name;
            }
            $category->save();

            /**
             * saving group
             */
            $group = $category->group()->where('name', $value)->first();
            if (is_null($group)) {
                $group = $category->group()->getModel()->newInstance();
            }
            $group->fill([
                'category_id' => $category->id,
                'name'        => $value
            ]);
            $group->save();
        });
        return true;
    }

    /**
     * Get model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->repository->make('Antares\Customfields\Model\FieldCategory')->newInstance();
    }

    /**
     * Get forms configuration from database
     * @return mixed
     */
    public function retrieveAll()
    {
        if (empty($this->forms)) {
            $data        = $this->getModel()->get();
            $this->forms = $this->processRetrievedData($data);
        }
        return $this->forms;
    }

    public function add($id, $location = 'parent', $callback = null)
    {
        
    }

}
