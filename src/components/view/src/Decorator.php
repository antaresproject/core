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
 namespace Antares\View;

use BadMethodCallException;

class Decorator
{
    /**
     * The registered custom macros.
     *
     * @var array
     */
    protected $macros = [];

    /**
     * Registers a custom macro.
     *
     * @param  string  $name
     * @param  \Closure  $macro
     *
     * @return void
     */
    public function macro($name, $macro)
    {
        $this->macros[$name] = $macro;
    }

    /**
     * Render the macro.
     *
     * @param  string  $name
     * @param  array   $data
     *
     * @return string
     *
     * @throws \BadMethodCallException
     */
    public function render($name, $data = null)
    {
        if (! isset($this->macros[$name])) {
            throw new BadMethodCallException("Method [$name] does not exist.");
        }

        return call_user_func($this->macros[$name], $data);
    }

    /**
     * Dynamically handle calls to custom macros.
     *
     * @param  string  $method
     * @param  array   $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        array_unshift($parameters, $method);

        return call_user_func_array([$this, 'render'], $parameters);
    }
}
