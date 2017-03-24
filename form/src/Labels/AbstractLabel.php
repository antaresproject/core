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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @author         Mariusz Jucha <mariuszjucha@gmail.com>
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Labels;

/**
 * @author Mariusz Jucha <mariuszjucha@gmail.com>
 * Date: 24.03.17
 * Time: 11:16
 */
abstract class AbstractLabel
{

    /** @var string */
    protected $name;
    
    /** @var array */
    protected $attributes;
    
    /**
     * @param $name
     * @return bool
     */
    public function hasAttribute(string $name) : bool
    {
        return isset($this->attributes, $name);
    }
    
    /**
     * @param $name
     * @param $value
     * @return AbstractLabel
     */
    public function setAttribute(string $name, $value) : AbstractLabel
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    
    /**
     * @param $name
     * @param $value
     * @return AbstractLabel
     */
    public function setAttributeIfNotExists($name, $value) : AbstractLabel
    {
        if (!$this->hasAttribute($name)) {
            $this->setAttribute($name, $value);
        }
        return $this;
    }
    
    /**
     * @param array $values
     * @return AbstractLabel
     */
    public function setAttributes(array $values) : AbstractLabel
    {
        $this->attributes = $values;
        return $this;
    }
    
    /**
     * @param string $name
     * @param null $fallbackValue
     * @return mixed
     */
    public function getAttribute(string $name, $fallbackValue = null)
    {
        if ($this->hasAttribute($name)) {
            return $this->attributes[$name];
        }
        
        return $this->setAttribute($name, $fallbackValue);
    }
    
    /**
     * @return array
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }
    
    /**
     * @return string
     */
    public function __toString() : string
    {
        try {
            return $this->render();
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * Render label to html
     *
     * @return string
     */
    abstract protected function render();
    
}