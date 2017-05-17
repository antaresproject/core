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

namespace Antares\Model\Memory;

use Illuminate\Support\Arr;
use Antares\Memory\Handler;
use Illuminate\Contracts\Container\Container;
use Antares\Contracts\Memory\Handler as HandlerContract;

class UserMetaRepository extends Handler implements HandlerContract
{

    /**
     * Storage name.
     *
     * @var string
     */
    protected $storage = 'user';

    /**
     * Cached user meta.
     *
     * @var array
     */
    protected $userMeta = [];

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
        return [];
    }

    /**
     * Get value from database.
     * @param  string   $key
     * @return mixed
     */
    public function retrieve($key)
    {
//        if (!is_array($key)) {
//            return;
//        }
        list($name, $userId) = explode('/user-', $key);

        if (!isset($this->userMeta[$userId])) {
            $data = $this->getModel()->where('user_id', '=', $userId)->get();

            $this->userMeta[$userId] = $this->processRetrievedData($userId, $data);
        }

        return Arr::get($this->userMeta, "{$userId}.{$name}");
    }

    /**
     * Add a finish event.
     *
     * @param  array  $items
     *
     * @return bool
     */
    public function finish(array $items = [])
    {
        foreach ($items as $key => $value) {
            $this->save($key, $value);
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
    protected function processRetrievedData($userId, $data = [])
    {
        $items = [];

        foreach ($data as $meta) {
            if (!$value = @unserialize($meta->value)) {
                $value = $meta->value;
            }

            $key = $meta->name;

            $this->addKey("{$key}/user-{$userId}", [
                'id'    => $meta->id,
                'value' => $value,
            ]);

            $items[$key] = $value;
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
        $isNew = $this->isNewKey($key);

        list($name, $userId) = explode('/user-', $key);

        if ($this->check($key, $value) || empty($userId)) {
            return;
        }

        $this->saving($name, $userId, $value, $isNew);
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
    protected function saving($name, $userId, $value = null, $isNew = true)
    {
        $meta = $this->getModel()->search($name, $userId)->first();

        if (is_null($value) || $value === ':to-be-deleted:') {
            !is_null($meta) && $meta->delete();
            return;
        }

        if (true === $isNew && is_null($meta)) {
            $meta = $this->getModel();

            $meta->name    = $name;
            $meta->user_id = $userId;
        }

        $meta->value = serialize($value);
        $meta->save();
    }

    /**
     * Get model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->repository->make('Antares\Model\UserMeta')->newInstance();
    }

    /**
     * Get user metas from database
     * @param  string $key
     * @return mixed
     */
    public function retrieveAll($userId)
    {
        if (!isset($this->userMeta[$userId])) {
            $data                    = $this->getModel()->where('user_id', '=', $userId)->get();
            $this->userMeta[$userId] = $this->processRetrievedData($userId, $data);
        }
        return Arr::get($this->userMeta, $userId);
    }

}
