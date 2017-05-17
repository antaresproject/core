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
use Antares\Memory\Provider;

class UserMetaProvider extends Provider
{

    /**
     * Get value of a key.
     *
     * @param  string  $key        A string of key to search.
     * @param  mixed   $default    Default value if key doesn't exist.
     *
     * @return mixed
     */
    public function get($key = null, $default = null)
    {


        $key   = str_replace('.', '/user-', $key);
        $value = Arr::get($this->items, $key);


                        if ($value === ':to-be-deleted:') {
            return value($default);
        }

                        if (!is_null($value)) {
            return $value;
        }

        if (is_null($value = $this->handler->retrieve($key))) {
            return value($default);
        }

        $this->put($key, $value);

        return $value;
    }

    /**
     * Set a value from a key.
     *
     * @param  string  $key        A string of key to add the value.
     * @param  mixed   $value      The value.
     *
     * @return mixed
     */
    public function put($key, $value = '')
    {
        $key   = str_replace('.', '/user-', $key);
        $value = value($value);

        $this->set($key, $value);

        return $value;
    }

    /**
     * Delete value of a key.
     *
     * @param  string   $key        A string of key to delete.
     *
     * @return bool
     */
    public function forget($key = null)
    {
        $key = str_replace('.', '/user-', $key);

        return Arr::set($this->items, $key, ':to-be-deleted:');
    }

    /**
     * all meta fields for user
     * @param numeric $userId
     * @return type
     */
    public function all($userId = null)
    {
        if (empty($this->items)) {
            return (is_null($userId)) ? parent::all() : $this->handler->retrieveAll($userId);
        }
        return $this->items;
    }

}
