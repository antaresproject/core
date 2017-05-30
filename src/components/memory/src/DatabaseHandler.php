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

use Antares\Contracts\Memory\Handler as HandlerContract;
use Illuminate\Support\Arr;

abstract class DatabaseHandler extends Handler implements HandlerContract
{

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
            $value = $memory->value;

            $items = Arr::add($items, $memory->name, $value);

            $this->addKey($memory->name, [
                'id'    => $memory->id,
                'value' => $value,
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
            if (!is_array($value)) {
                $value = serialize($value);

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
     * @param  string   $key
     * @param  mixed    $value
     * @param  bool     $isNew
     *
     * @return bool
     */
    abstract protected function save($key, $value, $isNew = false);

    /**
     * Get resolver instance.
     *
     * @return object
     */
    abstract protected function resolver();

    /**
     * Get items from cache.
     *
     * @return array
     */
    protected function getItemsFromCache()
    {
        return $this->cache->rememberForever($this->cacheKey, function () {
                    return $this->getItemsFromDatabase();
                });
    }

    /**
     * Get items from database.
     *
     * @return array
     */
    protected function getItemsFromDatabase()
    {
        return $this->resolver()->get();
    }

}
