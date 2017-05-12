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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Html;

use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Contracts\Container\Container;

abstract class Grid
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Grid attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Key map for column overwriting.
     *
     * @var array
     */
    protected $keyMap = [];

    /**
     * Meta attributes.
     *
     * @var array
     */
    protected $meta = [];

    /**
     * Form name
     *
     * @var String
     */
    protected $name;

    /**
     * Grid Definition.
     *
     * @var array
     */
    protected $definition = [
        'name'    => null,
        '__call'  => [],
        '__get'   => [],
        '__set'   => ['attributes', 'controls', 'rules'],
        '__isset' => [],
    ];

    /**
     * Create a new Grid instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;

        if (method_exists($this, 'initiate')) {
            $app->call([$this, 'initiate']);
        }
    }

    /**
     * Add or append Grid attributes.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @return array|null
     */
    public function attributes($key = null, $value = null)
    {
        if (is_null($key)) {
            return $this->attributes;
        }

        if (is_array($key)) {
            $this->attributes = array_merge($this->attributes, $key);
        } else {
            $this->attributes[$key] = $value;
        }

        return;
    }

    /**
     * Allow column overwriting.
     *
     * @param  string  $name
     * @param  mixed|null  $callback
     *
     * @return \Illuminate\Support\Fluent
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function of($name, $callback = null)
    {

        $type = $this->definition['name'];

        if (is_null($type) || !property_exists($this, $type)) {
            throw new RuntimeException('Not supported.');
        } elseif (!isset($this->keyMap[$name])) {
            throw new InvalidArgumentException("Name [{$name}] is not available.");
        }

        $id = $this->keyMap[$name];

        if (is_callable($callback)) {
            call_user_func($callback, $this->{$type}[$id]);
        }


        return $this->{$type}[$id];
    }

    /**
     * Forget meta value.
     *
     * @param  string  $key
     *
     * @return void
     */
    public function forget($key)
    {
        Arr::forget($this->meta, $key);
    }

    /**
     * Get meta value.
     *
     * @param  string  $key
     * @param  mixed|null  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->meta, $key, $default);
    }

    /**
     * Set meta value.
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return array
     */
    public function set($key, $value)
    {
        return Arr::set($this->meta, $key, $value);
    }

    /**
     * Build basic name, label and callback option.
     *
     * @param  mixed  $name
     * @param  mixed  $callback
     *
     * @return array
     */
    protected function buildFluentAttributes($name, $callback = null)
    {
        $label = $name;

        if (!is_string($label)) {
            $callback = $label;
            $name     = '';
            $label    = '';
        } elseif (is_string($callback)) {
            $name     = Str::lower($callback);
            $callback = null;
        } else {
            $name  = Str::lower($name);
            $label = Str::title($name);
        }

        return [$label, $name, $callback];
    }

    /**
     * Magic Method for calling the methods.
     *
     * @param  string  $method
     * @param  array   $parameters
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function __call($method, array $parameters)
    {
        unset($parameters);
        if (!in_array($method, $this->definition['__call'])) {
            throw new InvalidArgumentException("Unable to use __call for [{$method}].");
        }

        return $this->$method;
    }

    /**
     * Magic Method for handling dynamic data access.
     *
     * @param  string  $key
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function __get($key)
    {
        if (!in_array($key, $this->definition['__get'])) {
            throw new InvalidArgumentException("Unable to use __get for [{$key}].");
        }

        return $this->{$key};
    }

    /**
     * Magic Method for handling the dynamic setting of data.
     *
     * @param  string  $key
     * @param  array   $parameters
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function __set($key, $parameters)
    {
        if (!in_array($key, $this->definition['__set'])) {
            throw new InvalidArgumentException("Unable to use __set for [{$key}].");
        } elseif (!is_array($parameters)) {
            throw new InvalidArgumentException('Require values to be an array.');
        }

        $this->attributes($parameters, null);
    }

    /**
     * Magic Method for checking dynamically-set data.
     *
     * @param  string  $key
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function __isset($key)
    {
        if (!in_array($key, $this->definition['__isset'])) {
            throw new InvalidArgumentException("Unable to use __isset for [{$key}].");
        }

        return isset($this->{$key});
    }

}
