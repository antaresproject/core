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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
 namespace Antares\Contracts\Memory;

use Antares\Contracts\Support\DataContainer;

interface Provider extends DataContainer
{
    /**
     * Shutdown/finish method.
     *
     * @return bool
     */
    public function finish();

    /**
     * Remove a item key.
     *
     * @param  string  $key
     *
     * @return void
     */
    public function forget($key);

    /**
     * Get a item value.
     *
     * @param  string  $key
     * @param  mixed   $default
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Check if item key has a value.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Set a value from a key.
     *
     * @param  string  $key    A string of key to add the value.
     * @param  mixed   $value  The value.
     *
     * @return mixed
     */
    public function put($key, $value = '');
}
