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
            foreach ($options as $k => $v) {
                if (!$v instanceof Option) {
                    if (!is_array($v) && !is_object($v)) {
                        $v = new Option($k, $v);
                    } else {
                        throw new WrongSelectOptionFormatException('Wrong option format');
                    }
                }

                $this->attributes[$v->value] = $v;
            }
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

    public function render()
    {
        if ($this->value) {
            foreach ($this->valueOptions as $option) {
                $option->selected = (
                    (!is_array($this->value) && $this->value == $option->value) ||
                    (is_array($this->value) && in_array($option->value, $this->value))
                );
            }
        }

        return parent::render();
    }

}
