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

use Antares\Memory\Model\Test;
use Illuminate\Support\Arr;
use Antares\Memory\DefaultHandler;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\Container;

class Registry extends DefaultHandler
{

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
    protected $config = [
        'cache' => false,
    ];

    /**
     * Registry Singleton Instance
     * @var Registry
     */
    private static $oInstance = false;

    /**
     * singleton instance
     * @param String $name
     * @param array $config
     * @param Container $repository
     * @param Repository $cache
     * @return Self
     */
    public static function getInstance($name, array $config, Container $repository, Repository $cache)
    {
        if (self::$oInstance == false) {
            self::$oInstance = new Registry($name, $config, $repository, $cache);
        }
        return self::$oInstance;
    }

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
            if($model instanceof Test) {
                $model->value = $value;
                $model->save();
            }
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

}
