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
 * @author         Marcin Domański <marcin@domanskim.pl>
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Controls;

use Antares\Form\Contracts\Attributable;
use Antares\Form\Contracts\Wrapperable;
use Antares\Form\Decorators\AbstractDecorator;
use Antares\Form\Labels\AbstractLabel;
use Antares\Form\Labels\Label;
use Antares\Form\Traits\AttributesTrait;
use Antares\Form\Traits\WrapperTrait;
use Antares\Messages\MessageBag;

abstract class AbstractType implements Wrapperable, Attributable
{

    use AttributesTrait, WrapperTrait;

    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /** @var string|array */
    protected $value;

    /** @var bool */
    protected $hasLabel = false;

    /** @var AbstractLabel */
    protected $label;

    /** @var AbstractDecorator */
    protected $decorator;

    /** @var array */
    protected $messages = [];

    /** @var string */
    protected $orientation;

    /** @var string */
    public $prependHtml = '';

    /** @var string */
    public $appendHtml = '';

    /** @var array use this to change attributes of div wrapping input */
    public $inputWrapper = [];

    /** @var array use this to change attributes of div wrapping label */
    public $labelWrapper = [];

    /**
     * AbstractType constructor
     *
     * @param string $name
     * @param array $attributes
     */
    public function __construct(string $name, array $attributes = [])
    {
        $this->setName($name);
        $this->attributes = array_merge($attributes, ['name' => $this->getName()]);
        $this->wrapper    = ['class' => 'col-dt-12'];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @param AbstractLabel|string $label
     * @return AbstractType
     */
    public function setLabel($label, $info = ''): AbstractType
    {
        if (!$label instanceof AbstractLabel) {
            $label = new Label($label, $this, $info);
        }
        if (!$label->hasControl()) {
            $label->setControl($this);
        }
        $this->label = $label;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLabel(): bool
    {
        return $this->hasLabel;
    }

    /**
     * @return AbstractLabel
     */
    public function getLabel(): AbstractLabel
    {
        return $this->label;
    }

    /**
     * @param AbstractDecorator $decorator
     * @return AbstractType
     */
    public function setDecorator(AbstractDecorator $decorator)
    {
        $this->decorator = $decorator;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AbstractType
     */
    public function setName(string $name): AbstractType
    {
        $this->name = $name;
        return $this;
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
     * @return AbstractType
     */
    public function setType(string $type): AbstractType
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param array|string $value
     * @return AbstractType
     */
    public function setValue($value): AbstractType
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param string $placeholder
     * @return AbstractType
     */
    public function setPlaceholder($placeholder): AbstractType
    {
        return $this->setAttribute('placeholder', $placeholder);
    }

    /**
     * @param string $class
     * @return AbstractType
     */
    public function addClass($class): AbstractType
    {
        return $this->setAttribute('class',
            $this->hasAttribute('class')
                ? sprintf('%s %s', $this->getAttribute('class'), $class) : $class);
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param string $type
     * @param string $message
     * @return AbstractType
     */
    public function addMessage(string $type, string $message): AbstractType
    {
        $this->messages[$type][] = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrientation(): string
    {
        return $this->orientation;
    }

    /**
     * @param string $orientation
     *
     * @return self
     */
    public function setOrientation(string $orientation)
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * lookup for validation errors for this control
     */
    protected function findErrors()
    {
        $session = session();
        if (!$session->has('errors') || !$session->get('errors')->hasBag('default')) {
            return;
        }
        /** @var MessageBag $messageBag */
        $messageBag = session()->get('errors')->getBag('default');
        if (isset($messageBag->messages()[$this->name])) {
            foreach ($messageBag->messages()[$this->name] as $error) {
                $this->addError($error);
            }
        }
    }

    /**
     * @param string $error
     */
    public function addError(string $error)
    {
        $this->messages['errors'][] = $error;
    }

    /**
     * @return array
     */
    public function getInputWrapper(): array
    {
        return $this->inputWrapper;
    }

    /**
     * @param array $inputWrapper
     * @return AbstractType
     */
    public function setInputWrapper(array $inputWrapper)
    {
        $this->inputWrapper = $inputWrapper;
        return $this;
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
     * @return AbstractType
     */
    public function setLabelWrapper(array $labelWrapper)
    {
        $this->labelWrapper = $labelWrapper;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        try {
            return $this->decorator instanceof AbstractType
                ? $this->decorator->decorate($this) : $this->render();
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * Rendering this very control
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function renderControl()
    {
        return view('antares/foundation::form.controls.' . $this->type, ['control' => $this]);
    }

    /**
     * Render control to html
     *
     * @return string
     */
    protected function render()
    {
        $this->findErrors();
        $this->fixWrappers();
        if (!$this->label instanceof AbstractLabel && $this->type != 'hidden') {
            $this->setLabel(new Label(ucfirst(str_replace('_', ' ', $this->name))));
        }

        return view('antares/foundation::form.' . $this->orientation, [
            'label'       => ($this->label instanceof AbstractLabel) ? $this->getLabel()->render() : '',
            'input'       => $this->renderControl(),
            'orientation' => $this->orientation,
            'control'     => $this,
            'errors'      => $this->messages['errors']?? [],
        ]);
    }

    private function fixWrappers()
    {
        //
        /**
         * @TODO make orientation an object with predefined attributes
         */
        $labelWrapper = (isset($this->labelWrapper['class']) && !empty($this->labelWrapper['class']));
        $inputWrapper = (isset($this->inputWrapper['class']) && !empty($this->inputWrapper['class']));

        switch ($this->orientation) {
            case 'horizontal':
                if(!$labelWrapper) {
                    $this->labelWrapper['class'] = 'col-dt-2 col-2 col-mb-2';
                }
                if(!$inputWrapper) {
                    $this->inputWrapper['class'] = 'col-dt-6 col-6 col-mb-6';
                }
                break;
            case 'vertical':
                if(!$labelWrapper) {
                    $this->labelWrapper['class'] = 'child-align-top col-16 mb2';
                }
                if(!$inputWrapper) {
                    $this->inputWrapper['class'] = 'form-block col-dt-16 col-16 col-mb-16';
                }
                break;
            default:
                if(!$labelWrapper) {
                    $this->labelWrapper['class'] = 'col-dt-2 col-2 col-mb-2';
                }
                if(!$inputWrapper) {
                    $this->inputWrapper['class'] = 'col-dt-6 col-6 col-mb-6';
                }
                break;
        }

    }

}