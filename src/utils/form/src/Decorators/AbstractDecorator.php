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

namespace Antares\Form\Decorators;

use Antares\Form\Controls\AbstractType;
use Antares\Form\Labels\AbstractLabel;
use Antares\Form\Traits\WrapperTrait;

abstract class AbstractDecorator
{

    use WrapperTrait;

    /** @var string */
    protected $name;

    /** @var AbstractType */
    protected $control;

    /** @var array */
    protected $inputWrapper;

    /** @var array */
    protected $labelWrapper;

    /**
     * @return array
     */
    public function getInputWrapper(): array
    {
        return $this->inputWrapper;
    }

    /**
     * @param array $inputWrapper
     * @return AbstractDecorator
     */
    public function setInputWrapper(array $inputWrapper)
    {
        $this->inputWrapper = $inputWrapper;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return AbstractDecorator
     */
    public function addInputWrapper($name, $value)
    {
        if (isset($this->inputWrapper[$name])) {
            $this->inputWrapper[$name] .= ' ' . $value;
        } else {
            $this->inputWrapper[$name] = $value;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function addLabelWrapper($name, $value)
    {
        if (isset($this->labelWrapper[$name])) {
            $this->labelWrapper[$name] .= ' ' . $value;
        } else {
            $this->labelWrapper[$name] = $value;
        }
    }

    /**
     * @return array
     */
    public function getLabelWrapper(): array
    {
        return $this->labelWrapper;
    }

    /**
     * @param array $labelWrapper
     * @return AbstractDecorator
     */
    public function setLabelWrapper(array $labelWrapper)
    {
        $this->labelWrapper = $labelWrapper;
        return $this;
    }

    /**
     * Render control
     *
     * @param AbstractType $control
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render(AbstractType $control)
    {
        $this->control = $control;

        return view('antares/foundation::form.' . $this->name, [
            'label'        => ($this->control->getLabel() instanceof AbstractLabel)
                ? $this->control->getLabel()->render() : '',
            'input'        => $this->control->renderControl(),
            'control'      => $this->control,
            'errors'       => $this->control->getMessages()['errors'] ?? [],
            'inputWrapper' => $this->inputWrapper,
            'labelWrapper' => $this->labelWrapper,
            'decorator'    => $this
        ]);
    }

}
