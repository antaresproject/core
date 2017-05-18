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

class CheckboxType extends AbstractType
{

	/** @var string */
	protected $type = 'checkbox';

	/** @var bool */
	protected $useHiddenElement = false;

	/** @var int */
	protected $checkedValue = 1;

	/** @var int */
	protected $uncheckedValue = 0;

	/**
	 * @return string
	 */
	public function render()
	{
		$this->setAttribute('data-icheck', true);
		return parent::render();
	}

	/**
	 * @return bool
	 */
	public function isChecked(): bool
	{
		return $this->value === $this->getCheckedValue();
	}

	/**
	 * Checks or unchecks the checkbox
	 *
	 * @param bool $checked
	 * @return CheckboxType
	 */
	public function setChecked(bool $checked)
	{
		$this->value = $checked ? $this->getCheckedValue() : $this->getUncheckedValue();
		return $this;
	}

	/**
	 * @return bool
	 */
	public function useHiddenElement(): bool
	{
		return $this->useHiddenElement;
	}

	/**
	 * @param bool $useHiddenElement
	 * @return CheckboxType
	 */
	public function setUseHiddenElement(bool $useHiddenElement): CheckboxType
	{
		$this->useHiddenElement = $useHiddenElement;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCheckedValue(): int
	{
		return $this->checkedValue;
	}

	/**
	 * @param int $checkedValue
	 * @return CheckboxType
	 */
	public function setCheckedValue(int $checkedValue): CheckboxType
	{
		$this->checkedValue = $checkedValue;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getUncheckedValue(): int
	{
		return $this->uncheckedValue;
	}

	/**
	 * @param int $uncheckedValue
	 * @return CheckboxType
	 */
	public function setUncheckedValue(int $uncheckedValue): CheckboxType
	{
		$this->uncheckedValue = $uncheckedValue;
		return $this;
	}

	/**
	 * @param mixed $value
	 * @return AbstractType
	 */
	public function setValue($value): AbstractType
	{
		$this->value = (string) $value === (string) $this->getCheckedValue()
			? $this->getCheckedValue() : $this->getUncheckedValue();

		return $this;
	}

}
