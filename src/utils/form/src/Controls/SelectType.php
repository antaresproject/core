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
 * @author         Mariusz Jucha <mariuszjucha@gmail.com>
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Controls;

use Antares\Form\Controls\Elements\OptGroup;
use Antares\Form\Controls\Elements\Option;
use Antares\Form\Exceptions\WrongSelectOptionFormatException;
use Antares\Form\Traits\SelectTypeFunctionsTrait;

class SelectType extends AbstractType
{

    use SelectTypeFunctionsTrait;

    /** @var string */
    protected $type = 'select';

    /** @var Option[] */
    protected $valueOptions = [];

    /** @var string */
    protected $emptyValue;

    /**
     * @param $options
     * @return SelectType
     * @throws WrongSelectOptionFormatException
     */
    public function setValueOptions($options): SelectType
    {
        if (is_array($options) || $options instanceof \Traversable) {
            foreach ($options as $key => $value) {
                if (!$value instanceof Option) {
                    if (!is_array($value) && !is_object($value)) {
                        $value = new Option($key, $value);
                    } else {
                        $value = new OptGroup($key, $this->createOptionsFormArray($value));
                    }
                }

                $this->valueOptions[$value instanceof Option ? $value->value : $value->label] = $value;
            }
        }

        return $this;
    }
    
    /**
     * @param bool $multiple
     * @return SelectType
     */
    public function setMultiple(bool $multiple): SelectType
    {
        return $multiple ? $this->setAttribute('multiple', 'multiple')
            : $this->removeAttribute('multiple');
    }

	/**
	 * @return bool
	 */
    public function isMultiple(): bool
    {
        return $this->hasAttribute('multiple') && $this->getAttribute('multiple');
    }
    
    /**
     * @param $array
     * @return array
     */
    private function createOptionsFormArray($array): array
    {
        $options = [];
        
        foreach ($array as $key => $value) {
            $options[] = new Option($key, $value);
        }
        
        return $options;
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
    
    /**
     * @param Option $option
     */
    private function setSelectedAttribute(Option $option)
    {
        $option->selected = (
            (!is_array($this->value) && $this->value == $option->value) ||
            (is_array($this->value) && in_array($option->value, $this->value))
        );
    }
    
    /**
     * @return string
     */
    public function render()
    {
        if ($this->value) {
            foreach ($this->valueOptions as $option) {
                if ($option instanceof Option) {
                    $this->setSelectedAttribute($option);
                } else {
                    foreach ($option->options as $opt) {
                        $this->setSelectedAttribute($opt);
                    }
                }
            }
        }

        return parent::render();
    }

}
