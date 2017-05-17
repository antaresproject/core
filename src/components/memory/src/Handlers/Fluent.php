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

use Illuminate\Support\Arr;
use Antares\Memory\DatabaseHandler;
use Illuminate\Database\DatabaseManager;
use Illuminate\Contracts\Cache\Repository;

class Fluent extends DatabaseHandler
{

    /**
     * Storage name.
     *
     * @var string
     */
    protected $storage = 'fluent';

    /**
     * Memory configuration.
     *
     * @var array
     */
    protected $config = [
        'cache' => false,
    ];

    /**
     * Setup a new memory handler.
     *
     * @param  string  $name
     * @param  array  $config
     * @param  \Illuminate\Database\DatabaseManager  $repository
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     */
    public function __construct($name, array $config, DatabaseManager $repository, Repository $cache)
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
        $count = $this->resolver()->where('name', '=', $key)->count();
        $id    = $this->getKeyId($key);
        if (true === $isNew && $count < 1) {
            $this->resolver()->insert([
                'name'  => $key,
                'value' => $value,
            ]);
        } else {
            $this->resolver()->where('id', '=', $id)->update([
                'value' => $value,
            ]);
        }
    }

    /**
     * Get resolver instance.
     *
     * @return object
     */
    protected function resolver()
    {
        $table = Arr::get($this->config, 'table', $this->name);

        return $this->repository->table($table);
    }

}
