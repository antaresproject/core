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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Traits;


/**
 * @author Marcin Domański <marcin@domanskim.pl>
 * Date: 27.03.17
 * Time: 12:40
 */
trait AttributesTrait
{

    /** @var array */
    protected $attributes = [];


    /**
     * @param $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setAttribute(string $name, $value): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setAttributeIfNotExists($name, $value): self
    {
        if (!$this->hasAttribute($name)) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function setAttributes(array $values): self
    {
        $this->attributes = $values;

        return $this;
    }

    /**
     * @param string $name
     * @param null   $fallbackValue
     * @return mixed
     */
    public function getAttribute(string $name, $fallbackValue = null)
    {
        if ($this->hasAttribute($name)) {
            return $this->attributes[$name];
        }

        $this->setAttribute($name, $fallbackValue);

        return $this->getAttribute($name);
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeAttribute($name): self
    {
        if($this->hasAttribute($name)) {
            unset($this->attributes[$name]);
        }

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function addAttribute($name, $value): self
    {
        if ($this->hasAttribute($name)) {
            $this->attributes[$name] .= ' ' . $value;
        } else {
            $this->attributes[$name] = $value;
        }

        return $this;
    }

}