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

use Antares\Form\Controls\AbstractType;

/**
 * @author Mariusz Jucha <mariuszjucha@gmail.com>
 * Date: 24.03.17
 * Time: 11:16
 */
abstract class AbstractLabel
{

    /** @var string */
    public $name;
    
    /** @var array */
    protected $attributes;

    /** @var string */
    public $type;
    /** @var string */
    public $wrapper;
    /** @var AbstractType */
    protected $control;

    /**
     * AbstractLabel constructor.
     *
     * @param string            $name
     * @param AbstractType|null $control
     * @param array             $attributes
     */
    public function __construct(string $name, AbstractType $control = null, array $attributes = [])
    {
        $this->name = $name;
        $this->control = $control;
        $this->setAttributes($attributes);
    }
    
    /**
     * @param $name
     * @return bool
     */
    public function hasAttribute(string $name) : bool
    {
        return isset($this->attributes[$name]);
    }
    
    /**
     * @param $name
     * @param $value
     * @return self
     */
    public function setAttribute(string $name, $value) : self
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    
    /**
     * @param $name
     * @param $value
     * @return self
     */
    public function setAttributeIfNotExists($name, $value) : self
    {
        if (!$this->hasAttribute($name)) {
            $this->setAttribute($name, $value);
        }
        return $this;
    }
    
    /**
     * @param array $values
     * @return self
     */
    public function setAttributes(array $values) : self
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function hasControl(): bool 
    {
        return $this->control instanceof AbstractType;
    }

    /**
     * @param AbstractType $control
     */
    public function setControl(AbstractType $control)
    {
        $this->control = $control;
    }


    public function render()
    {
        return view('antares/foundation::form.labels.' . $this->type,
            ['label' => $this, 'control' => $this->control])->render();
    }

}