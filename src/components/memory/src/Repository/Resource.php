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


namespace Antares\Memory\Repository;

use Antares\Foundation\Repository\AbstractRepository;
use Antares\Memory\Model\ResourceMap;
use Illuminate\Container\Container;
use Illuminate\Cache\Repository;
use Exception;

class Resource extends AbstractRepository
{

    /**
     * cache repository instance
     *
     * @var Repository 
     */
    protected $cache;

    /**
     * cache name
     *
     * @var String 
     */
    protected $cacheKey = 'antares-resources';

    /**
     * Resource constructor.
     * 
     * @param Container $app
     * @param Repository $cache
     */
    public function __construct(Container $app, Repository $cache)
    {
        parent::__construct($app);
        $this->cache = $cache;
        $this->makeModel();
    }

    /**
     * name of repositroy model
     * 
     * @return Logs
     */
    public function model()
    {
        return ResourceMap::class;
    }

    /**
     * finds all resources
     * 
     * @return type
     */
    public function findAll()
    {
        return $this->cache->rememberForever($this->cacheKey, function() {
                    return $this->model->get();
                });
    }

    /**
     * add resource do resource stack
     * 
     * @param array $attributes
     * @return \Antares\Memory\Repository\Resource
     */
    public function add(array $attributes = array())
    {
        if (!$this->contains($attributes)) {
            $this->model->getModel()->newInstance($attributes)->save();
            $this->cache->forget($this->cacheKey);
        }
        return $this;
    }

    /**
     * whether resource stack contains parametrized row with component and action name
     * 
     * @param array $attributes
     * @return boolean
     */
    protected function contains(array $attributes = array())
    {
        $collection = $this->findAll();
        $filtered   = $collection->filter(function($value, $key) use($attributes) {
            foreach ($attributes as $keyname => $attrVal) {
                if ($value->{$keyname} != $attrVal) {
                    return false;
                }
            }
            return true;
        });
        return $filtered->count() > 0;
    }

    /**
     * find item by attributes
     * 
     * @param array $attributes
     * @param array $columns
     * @return mixed
     * @throws Exception
     */
    public function findOneByAttributes(array $attributes = array(), $columns = array('*'))
    {
        if (empty($attributes)) {
            throw new Exception('Unable to find entity');
        }
        return $this->model->where($attributes)->first($columns);
    }

}
