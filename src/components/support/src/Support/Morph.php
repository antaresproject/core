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
 namespace Antares\Support;

use RuntimeException;

abstract class Morph
{
    /**
     * Method prefix.
     *
     * @var string
     */
    public static $prefix = '';

    /**
     * Magic method to call passtru PHP functions.
     *
     * @param  string  $method
     * @param  array   $parameters
     *
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $parameters)
    {
        if (! static::isCallable($method)) {
            throw new RuntimeException("Unable to call [{$method}].");
        }

        return call_user_func_array(static::$prefix.snake_case($method), $parameters);
    }

    /**
     * Determine if method is callable.
     *
     * @param  string  $method
     *
     * @return bool
     */
    public static function isCallable($method)
    {
        return is_callable(static::$prefix.snake_case($method));
    }
}
