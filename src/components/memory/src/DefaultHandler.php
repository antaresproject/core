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
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Cache\Repository;
use Antares\Contracts\Memory\Handler as HandlerContract;
use Exception;

abstract class DefaultHandler extends Handler implements HandlerContract
{

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
     * Load the data from database.
     *
     * @return array
     */
    public function initiate()
    {
        $items = [];

        $memories = $this->cache instanceof Repository ? $this->getItemsFromCache() : $this->getItemsFromDatabase();
        foreach ($memories as $memory) {
            $key = $memory->name;

            $items = Arr::add($items, $key, $memory->value);
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
        $items   = array_dot($items);

        foreach ($items as $key => $value) {
            $isNew = $this->isNewKey($key);
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
     * Get items from cache.
     *
     * @return array
     */
    protected function getItemsFromCache()
    {
        return 'cli' === PHP_SAPI ? $this->getItemsFromDatabase() : $this->cache->rememberForever($this->cacheKey, function () {
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
        try {
            return $this->resolver()->get();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * forcing delete cache
     * @return boolean
     */
    public function forceForgetCache()
    {
        return !is_null($this->cache) ? $this->cache->forget($this->cacheKey) : true;
    }

}
