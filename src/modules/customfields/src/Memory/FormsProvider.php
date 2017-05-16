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

use Illuminate\Support\Arr;
use Antares\Memory\Provider;

class FormsProvider extends Provider
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
        $this->set($key, value($value));
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
        return Arr::set($this->items, $key, ':to-be-deleted:');
    }

    /**
     * all customfields fields
     * @return type
     */
    public function all()
    {
        if (empty($this->items)) {
            return $this->handler->retrieveAll();
        }
        return $this->items;
    }

}
