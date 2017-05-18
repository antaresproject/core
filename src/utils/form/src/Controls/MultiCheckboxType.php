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

class MultiCheckboxType extends AbstractType
{

	/** @var string */
	protected $type = 'multi_checkbox';

	/** @var bool */
	protected $useHiddenElement = false;

	/** @var int */
	protected $uncheckedValue = '';

	/** @var Option[] */
	protected $valueOptions = [];

	/**
	 * @param $options
	 * @return SelectType
	 * @throws WrongSelectOptionFormatException
	 */
	public function setValueOptions($options): MultiCheckboxType
	{
		if (is_array($options) || $options instanceof \Traversable) {
			foreach ($options as $key => $value) {
				if (!$value instanceof Option) {
					if (!is_array($value) && !is_object($value)) {
						$value = new Option($key, $value);
						$value->setAttribute('data-icheck', true);
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
	 * @param $array
	 * @return array
	 */
	private function createOptionsFormArray($array): array
	{
		$options = [];

		foreach ($array as $key => $value) {
			$options[] = (new Option($key, $value))
				->setAttribute('data-icheck', true);
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
