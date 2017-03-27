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

namespace Antares\Form\Controls;

class SelectType extends AbstractType
{
    
    /** @var string */
    protected $type = 'select';
    
    /** @var array */
    protected $valueOptions = [];
    
    /** @var string */
    protected $emptyValue;
    
    /**
     * @param array|\Traversable $options
     * @return SelectType
     */
    public function setValueOptions($options): SelectType
    {
        if (is_array($options) || $options instanceof \Traversable) {
            $this->valueOptions = $options;
        }
        
        return $this;
    }
    
    /**
     * @return array|\Traversable
     */
    public function getValueOptions()
    {
        return $this->valueOptions;
    }
    
    /**
     * @return bool
     */
    public function hasEmptyValue(): bool
    {
        return !empty($this->emptyValue);
    }
    
    /**
     * @return string
     */
    public function getEmptyValue(): string
    {
        return $this->emptyValue;
    }
    
    /**
     * @param string $emptyValue
     * @return SelectType
     */
    public function setEmptyValue(string $emptyValue): SelectType
    {
        $this->emptyValue = $emptyValue;
        
        return $this;
    }
    
}
