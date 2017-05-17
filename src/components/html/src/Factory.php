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
use Illuminate\Contracts\Container\Container;

abstract class Factory
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Factory instances.
     *
     * @var array
     */
    protected $names = [];

    /**
     * Construct a new factory.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Create a new Builder instance.
     *
     * @param  \Closure|null  $callback
     *
     * @return object
     */
    abstract public function make(Closure $callback = null);

    /**
     * Create a new builder instance of a named builder.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     *
     * @return object
     */
    public function of($name, Closure $callback = null)
    {
        if (!isset($this->names[$name])) {
            $this->names[$name]       = $this->make($callback);
            $this->names[$name]->name = $name;
        }
        $form = $this->names[$name];
        event('form.' . $name, [&$form]);
        return $form;
    }

    /**
     * get forms container
     * 
     * @return array
     */
    public function getNames()
    {
        return $this->names;
    }

    public function add($name, $grid = null)
    {
        if (!isset($this->names[$name])) {
            $this->names[$name]       = new \Antares\Html\Form\FormBuilder($grid);
            $this->names[$name]->name = $name;
        }

        return $this->names[$name];
    }

}
