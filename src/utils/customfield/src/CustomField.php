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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Customfield;

use Antares\Customfields\Model\FieldView;
use Antares\Customfields\Model\FieldType;
use Antares\Customfields\Model\FieldData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Factory;
use Antares\Html\Form\Field;
use Closure;

class CustomField extends Field
{

    /**
     * Name of customfield
     *
     * @var String 
     */
    protected $name = null;

    /**
     * Validation rules
     *
     * @var array 
     */
    protected $rules = [];

    /**
     * Field attributes
     *
     * @var array 
     */
    protected $attributes = [];

    /**
     * Whether customfield is configurable in web interface
     *
     * @var boolean 
     */
    protected $configurable = true;

    /**
     * Validator instance
     *
     * @var Factory 
     */
    protected $validator;

    /**
     * Model instance
     *
     * @var Model 
     */
    protected $model = null;

    /**
     * FieldView instance
     *
     * @var \Antares\Customfields\Model\FieldView 
     */
    protected $field;

    /**
     * Whether field should be automatically displayed in form
     *
     * @var type 
     */
    protected $formAutoDisplay = false;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct([]);
        $this->validator = app(Factory::class);
    }

    /**
     * Field attributes setter
     * 
     * @param FieldView $field
     * @return $this
     */
    public function attributes(FieldView $field)
    {
        if (is_null($field)) {
            return $this;
        }

        if (!is_null($field->label)) {
            $this->attributes['label'] = $field->label;
        }
        if (!is_null($field->description)) {
            $this->attributes['help'] = $field->description;
        }

        if (!is_null($field->placeholder)) {
            array_set($this->attributes, 'attributes.placeholder', $field->placeholder);
        }

        if (!empty($field->additional_attributes)) {
            $attrs                = explode(';', $field->additional_attributes);
            $additionalAttributes = [];
            array_map(function($element) use(&$additionalAttributes) {
                @list($name, $value) = explode(':', $element);
                $additionalAttributes[$name] = $value;
            }, $attrs);
            array_set($this->attributes, 'attributes', array_merge($this->attributes['attributes'], $additionalAttributes));
        }

        $this->id                 = $this->id ?? $field->name;
        $this->name               = $this->name ?? $field->name;
        $this->type               = $this->type ?? $field->type_name . (($field->type) ? ':' . $field->type : '');
        $this->value              = $this->value ?? $field->value;
        $this->attributes['name'] = $field->name;
        if ($field->option_values && !isset($this->attributes['options'])) {
            $options = explode(';', $field->option_values);
            foreach ($options as $option) {
                @list($value, $label) = explode(':', $option);
                $this->attributes['options'][$value] = $label;
            }
        }
        if (is_null($this->field)) {
            $this->field = $field;
        }

        $validatorOptions = explode(';', $field->validators_config);
        $options          = [];
        foreach ($validatorOptions as $option) {
            @list($id, $value) = explode(':', $option);
            array_set($options, $id, $value);
        }
        $validators = explode(';', $field->validators);
        $config     = [];
        foreach ($validators as $validator) {
            @list($id, $name) = explode(':', $validator);
            if ($name == 'custom') {
                continue;
            }
            if (isset($options[$id])) {
                $name = $name . ':' . $options[$id];
            }
            array_set($config, $id, $name);
        }

        if (!isset($this->rules[$this->name])) {
            $this->rules[$this->name] = [];
        }
        $this->rules[$this->name] = array_unique(array_merge($this->rules[$this->name], $config));

        return $this;
    }

    /**
     * Field attribute setter
     * 
     * @param Closure $value
     * @return $this
     */
    public function setField(Closure $value)
    {
        $this->onValidate();
        $this->attributes['field'] = $value;
        @list($name, $type) = explode(':', $this->attributes['type']);
        $whereType                 = [
            'name' => $name,
        ];
        if (!is_null($type)) {
            array_set($whereType, 'type', $type);
        }

        $typeModel = FieldType::query()->where($whereType)->firstOrFail();
        $where     = [
            'name'          => $this->name,
            'brand_id'      => brand_id(),
            'imported'      => 1,
            'type_id'       => $typeModel->id,
            'force_display' => 1
        ];
        $field     = FieldView::query()->where($where)->first();
        if (is_null($field)) {
            return $this;
        }
        $this->attributes($field);


        return $this;
    }

    /**
     * Gets validation rules
     * 
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Validates customfield
     */
    public function onValidate()
    {
        return;
    }

    /**
     * Gets value query attributes
     * 
     * @param Model $model
     * @return array
     */
    private function queryAttributes(Model $model)
    {
        $data = [
            'user_id'    => user()->id,
            'namespace'  => get_class($model),
            'foreign_id' => $model->id
        ];
        (is_null($this->field)) ? array_set($data, 'field_class', get_called_class()) : array_set($data, 'field_id', $this->field->id);
        return $data;
    }

    /**
     * On save customfield data
     * 
     * @param Model $model
     */
    public function onSave(Model $model)
    {
        $data            = $this->queryAttributes($model);
        $fieldData       = FieldData::query()->firstOrCreate($data);
        $fieldData->data = input($this->getName());
        return $fieldData->save();
    }

    /**
     * Gets customfield value
     * 
     * @return mixed
     */
    public function getValue()
    {
        $data      = $this->queryAttributes($this->model);
        $fieldData = FieldData::query()->where($data)->first(['data']);
        return !is_null($fieldData) ? $fieldData->data : null;
    }

    /**
     * Model setter
     * 
     * @param mixed $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Name getter
     * 
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Configurable property getter
     * 
     * @return boolean
     */
    public function configurable()
    {
        return $this->configurable;
    }

    /**
     * Attaches database field cofiguration
     * 
     * @param mixed $field
     * @return $this
     */
    public function attach($field = null)
    {
        if (!is_null($field)) {
            $this->field = $field;
        }
        return $this;
    }

    /**
     * Form auto display getter
     * 
     * @return boolean
     */
    public function formAutoDisplay()
    {
        return ($this->field) ? $this->field->force_display : $this->formAutoDisplay;
    }

    public function getFieldset()
    {
        return ($this->field && !empty($this->field->fieldsets)) ? $this->field->fieldsets[0]->name : false;
    }

}
