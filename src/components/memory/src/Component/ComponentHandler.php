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

namespace Antares\Memory\Component;

use Antares\Contracts\Memory\Handler as HandlerContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Antares\Support\Facades\Memory;
use Antares\Memory\DefaultHandler;
use Illuminate\Cache\Repository;
use Illuminate\Support\Arr;
use Exception;

class ComponentHandler extends DefaultHandler implements HandlerContract
{

    /**
     *
     * default brand id
     * 
     * @var numeric
     */
    private $defaultBrandId = null;

    /**
     * Storage name.
     *
     * @var string
     */
    protected $storage = 'eloquent';

    /**
     * finder instance
     *
     * @var \Antares\Extension\FilesystemFinder
     */
    protected $finder;

    /**
     * Memory keys ignored configuration.
     *
     * @var array
     */
    protected $ignoredPatterns = [
        '/^acl_antares/i',
        '/^extension_antares/i',
        '/extensions/'
    ];
    protected $cache;

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
        $this->finder     = app(\Antares\Extension\FilesystemFinder::class);
        $this->cacheKey   = "db-memory:{$this->storage}-{$this->name}-{$this->getDefaultBrandId()}";
        if (Arr::get($this->config, 'cache', false)) {
            $this->cache = $cache;
        }
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
     * Initiate the instance.
     * @return array
     */
    public function initiate()
    {
        $items    = [];
        $memories = $this->cache instanceof Repository ? $this->getItemsFromCache() : $this->getItemsFromDatabase();
        foreach ($memories as $key => $value) {
            if ($key === 'extensions') {
                foreach ($value['available'] as $keyname => $settings) {
                    $this->addKey('available.' . $keyname, ['value' => serialize($settings)]);
                }
                foreach ($value['active'] as $keyname => $settings) {
                    $this->addKey('active.' . $keyname, ['value' => serialize($settings)]);
                }
                $items = Arr::add($items, $key, $value);
            } else {
                $items = Arr::add($items, $key, $value);
                $this->addKey($key, ['value' => serialize($value)]);
            }
        }
        return $items + app('antares.memory')->make('primary')->all();
    }

    /**
     * Verify checksum.
     * @param  string  $name
     * @param  string  $check
     * @return bool
     */
    protected function check($name, $check = '')
    {
        return (Arr::get($this->keyMap, "{$name}.checksum") === $this->generateNewChecksum($check));
    }

    /**
     * @param array $items
     * @return array
     */
    protected function clear(array $items = [])
    {
        $cleared = [];
        foreach ($items as $name => $item) {
            $pass = true;
            foreach ($this->ignoredPatterns as $ignoredPattern) {
                if (preg_match($ignoredPattern, $name)) {
                    $pass = false;
                    break;
                }
            }
            if (!$pass) {
                $cleared[$name] = $item;
            }
        }
        return $cleared;
    }

    /**
     * @param type $key
     * @param type $value
     * @param type $brandId
     * @return boolean
     */
    protected function write($key, $value, $brandId = null, $flag = null)
    {
        $isNew = $this->isNewKey($key);
        if (!$this->check($key, serialize($value))) {
            $keyname = str_replace(['active.', 'available.'], '', $key);
            $this->save($keyname, $value, $isNew, $brandId, $flag);
            return true;
        }
        return false;
    }

    /**
     * @param array $items
     * @param numeric $brandId
     */
    protected function verify(array $items = [], $brandId = null)
    {

        $items   = $this->clear($items);
        $changed = [];
        foreach ($items as $key => $value) {
            if (strpos($key, 'extension_') !== false) {
                continue;
            }
            if ($key === 'extensions') {
                foreach ($value['available'] as $keyname => $settings) {
                    $changed[] = $this->write('available.' . $keyname, $settings, $brandId, 'available');
                }
                if (!isset($value['active'])) {
                    continue;
                }
                foreach ($value['active'] as $keyname => $settings) {
                    $changed[] = $this->write('active.' . $keyname, $settings, $brandId, 'active');
                }
            } else {
                $changed[] = $this->write($key, $value, $brandId);
            }
        }
        $countActive    = isset($this->keyMap['active']) ? count($this->keyMap['active']) : 0;
        $countAvailable = isset($this->keyMap['available']) ? count($this->keyMap['available']) : 0;

        $changed[] = isset($items['extensions']['active']) && (($countActive != count($items['extensions']['active'])));
        $changed[] = isset($items['extensions']['available']) && (($countAvailable != count($items['extensions']['available'])));

        $return = array_where($changed, function ($value, $key) {
            return $value == true;
        });

        return !empty($return);
    }

    /**
     * Add a finish event.
     * @param  array  $items
     * @return bool
     */
    public function finish(array $items = [])
    {
        $changed = isset($items['terminate']) && $items['terminate'] === 1 ? true : $this->verify($items);
        if ($changed && $this->cache instanceof Repository) {
            $this->cache->forget($this->cacheKey);
        }
    }

    /**
     * updates elements in container
     * @param array $items
     */
    public function update(array $items = [])
    {
        $brandId = $this->getDefaultBrandId();
        $changed = $this->verify($items, $brandId);

        Memory::make('component.default')->put('terminate', (int) $changed);
        if ($changed) {
            $this->keyMap = [];
        }
        if ($changed && $this->cache instanceof Repository) {
            $this->cache->forget($this->cacheKey);
        }
    }

    /**
     * get default brand
     * 
     * @return numeric
     */
    protected function getDefaultBrandId()
    {
        try {
            if (is_null($this->defaultBrandId)) {
                $this->defaultBrandId = 1;
            }
            return $this->defaultBrandId;
        } catch (Exception $e) {
            Log::emergency($e);
            return false;
        }
    }

    /**
     * Get items from database.
     * @return array
     */
    protected function getItemsFromDatabase()
    {
        $brandId = $this->getDefaultBrandId();
        try {
            return $this->resolver()->getAll($brandId);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * get handler model instance
     * @return Eloquent
     */
    protected function resolver()
    {
        $model = Arr::get($this->config, 'model', $this->name);
        return app()->make($model)->newInstance();
    }

    /**
     * @param type $key
     * @param type $value
     * @param type $isNew
     */
    protected function save($key, $value, $isNew = false)
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function forgetCache()
    {
        parent::forgetCache();
        $brandId = $this->getDefaultBrandId();
        Cache::forget('permissions_' . $brandId);
    }

}
