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


namespace Antares\Html;

use Closure;
use InvalidArgumentException;
use Antares\Contracts\Html\Builder as BuilderContract;
use Antares\Contracts\Html\Form\ClientScript;

abstract class Builder implements BuilderContract
{

    /**
     * Container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Grid instance.
     *
     * @var object
     */
    protected $grid;

    /**
     * Name of builder.
     *
     * @var string
     */
    public $name = null;

    /**
     * ClientScript instance
     *
     * @var ClientScript
     */
    public $clientScript = null;

    /**
     * Extend decoration.
     *
     * @param  \Closure  $callback
     *
     * @return $this
     */
    public function extend(Closure $callback = null)
    {
        !is_null($callback) && call_user_func($callback, $this->grid, $this->container->make('request'), $this->container->make('translator'));

        return $this;
    }

    /**
     * Magic method to get Grid instance.
     *
     * @param  string  $key
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function __get($key)
    {
        if (!in_array($key, ['grid', 'name'])) {
            throw new InvalidArgumentException("Unable to get property [{$key}].");
        }

        return $this->{$key};
    }

    /**
     * An alias to render().
     *
     * @return string
     *
     * @see static::render()
     */
    public function __toString()
    {
        return $this->render();
    }

}
