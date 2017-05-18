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


namespace Antares\Support\Traits;

use Illuminate\Support\Arr;

trait DataContainerTrait
{

    /**
     * Item or collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Get a item value.
     *
     * @param  string  $key
     * @param  mixed   $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $value = Arr::get($this->items, $key, $default);

        if (is_null($value)) {
            return value($default);
        }

        return $value;
    }

    /**
     * Set a item value.
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return mixed
     */
    public function set($key, $value = null)
    {
        return Arr::set($this->items, $key, value($value));
    }

    /**
     * Check if item key has a value.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function has($key)
    {
        return !is_null($this->get($key));
    }

    /**
     * Remove a item key.
     *
     * @param  string  $key
     *
     * @return void
     */
    public function forget($key)
    {
        Arr::forget($this->items, $key);
    }

    /**
     * Get all available items.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * add item into container without dotting key
     * 
     * @param String $key
     * @param mixed $value
     * @return mixed
     */
    public function push($key, $value = '')
    {
        $this->items[$key] = value($value);
        return $value;
    }

}
