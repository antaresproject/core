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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Traits;

use Illuminate\Support\Arr;

trait ComponentTrait
{

    /**
     * Getting rules attached to form model
     * 
     * @return array | null | mixed
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return String
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return String
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Gets single attribute
     * 
     * @param String | mixed $keyname
     * @param String | mixed $default
     * 
     * @return mixed
     * 
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Getting attributes of ui component
     * 
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Gets single attribute
     * 
     * @param String | mixed $keyname
     * @param String | mixed $default
     * 
     * @return mixed
     * 
     */
    public function getAttribute($key, $default = null)
    {
        return $this->get($key, $default);
    }

    /**
     * Gets single param
     * 
     * @param String | mixed $keyname
     * @param String | mixed $default
     * 
     * @return mixed
     * 
     */
    public function getParam($key, $default = null)
    {
        return Arr::get($this->params, $key, $default);
    }

    /**
     * Setting additional attributes
     * 
     * @param String $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (!array_key_exists($name, $this->attributes)) {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * Gets defaults attributes
     * 
     * @param array | mixed $args
     * @return array
     */
    public function getDefaults($args = null)
    {
        if (!is_null($args)) {
            if (is_array($args)) {
                return array_only($this->defaults, $args);
            }
            if (array_key_exists($args, $this->defaults)) {
                return $this->defaults[$args];
            }
        }
        return $this->defaults;
    }

    /**
     * View setter
     * 
     * @param \Illuminate\View\View $view
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Does the ui component has editable attributes
     * 
     * @return boolean
     */
    protected function editable()
    {
        if (array_key_exists('editable', $this->attributes) && $this->attributes['editable'] == false) {
            return false;
        }
        return in_array('form', get_class_methods($this));
    }

    /**
     * Does the ui component is dispatchable
     * 
     * @param String $className
     * @return boolean
     */
    protected function dispatchable($className)
    {
        return class_exists($className);
    }

}
