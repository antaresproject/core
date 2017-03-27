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

use Antares\Form\Decorators\AbstractDecorator;
use Antares\Form\Labels\AbstractLabel;
use Antares\Form\Labels\Label;
use Antares\Messages\MessageBag;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\ViewErrorBag;

abstract class AbstractType
{

    /** @var string */
    protected $id;
    
    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /** @var array */
    protected $attributes = [];

    /** @var string|array */
    protected $value;

    /** @var bool */
    protected $hasLabel = false;

    /** @var AbstractLabel */
    protected $label;

    /** @var array */
    public $wrapper;

    /** @var AbstractDecorator */
    protected $decorator;

    /** @var array */
    protected $messages = [];

    /** @var string */
    protected $orientation;

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
        $this->wrapper = ['class'];
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
    public function setLabel($label): AbstractType
    {
        if (!$label instanceof AbstractLabel) {
            $label = new Label($label);
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
    public function hasLabel() : bool
    {
        return $this->hasLabel;
    }
    
    /**
     * @return AbstractLabel
     */
    public function getLabel() : AbstractLabel
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
     * @return AbstractType
     */
    public function setAttribute(string $name, $value) : AbstractType
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return AbstractType
     */
    public function setAttributeIfNotExists($name, $value) : AbstractType
    {
        if (!$this->hasAttribute($name)) {
            $this->setAttribute($name, $value);
        }
        return $this;
    }

    /**
     * @param array $values
     * @return AbstractType
     */
    public function setAttributes(array $values) : AbstractType
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
    public function getAttributes() : array
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AbstractType
     */
    public function setName(string $name) : AbstractType
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return AbstractType
     */
    public function setType(string $type) : AbstractType
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
    public function setValue($value) : AbstractType
    {
        $this->value = $value;
        return $this;
    }
    
    /**
     * @param string $placeholder
     * @return AbstractType
     */
    public function setPlaceholder($placeholder) : AbstractType
    {
        return $this->setAttribute('placeholder', $placeholder);
    }
    
    /**
     * @param string $class
     * @return AbstractType
     */
    public function addClass($class) : AbstractType
    {
        return $this->setAttribute('class',
            $this->hasAttribute('class')
                ? sprintf('%s %s', $this->getAttribute('class'), $class) : $class);
    }
    
    /**
     * @return array
     */
    public function getMessages() : array
    {
        return $this->messages;
    }
    
    /**
     * @param string $type
     * @param string $message
     * @return AbstractType
     */
    public function addMessage(string $type, string $message) : AbstractType
    {
        $this->messages[$type][] = $message;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasWrapper(): bool
    {
        return !empty($this->wrapper);
    }

    /**
     * @return array
     */
    public function getWrapper(): array
    {
        return $this->wrapper;
    }

    /**
     * @param array $wrapper
     * @return AbstractType
     */
    public function setWrapper(array $wrapper)
    {
        $this->wrapper = $wrapper;
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
     */
    public function setOrientation(string $orientation)
    {
        $this->orientation = $orientation;
    }

    /**
     * lookup for validation errors for this control
     */
    private function findErrors()
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
     * @return string
     */
    public function __toString() : string
    {
        try {
            return $this->decorator instanceof AbstractType
                ? $this->decorator->decorate($this) : $this->render();
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * Render control to html
     *
     * @return string
     */
    protected function render()
    {
        $this->findErrors();
        $input = view('antares/foundation::form.controls.' . $this->type, ['control' => $this]);

        return view('antares/foundation::form.' . $this->orientation, [
            'label'   => $this->getLabel()->render(),
            'input'   => $input,
            'control' => $this,
            'errors'  => $this->messages['errors']?? [],
        ]);

    }

}