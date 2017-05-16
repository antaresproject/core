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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\Events;

use Antares\Html\Form\Fieldset;
use Antares\Contracts\Html\Form\Grid as FormGrid;
use Antares\Contracts\Html\Form\Builder as FormBuilder;
use Antares\Customfields\Model\FieldView;
use Antares\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;
use Antares\Customfields\Model\FieldData;
use ArrayAccess;
use Illuminate\Support\Facades\Log;

class FormHandler
{

    /**
     * The config implementation.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * customfields saved values
     */
    protected $values;

    /**
     * @var Antares\Customfields\Model\FieldData 
     */
    protected $fieldData;

    /**
     * Construct a new config handler.
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct(Request $request, FieldData $fieldData)
    {
        $this->request   = $request;
        $this->fieldData = $fieldData;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @param  \Antares\Contracts\Html\Form\Builder  $form
     *
     * @return void
     */
    public function onViewForm($model, FormBuilder $form, $eventName = null)
    {
        if (is_null($eventName)) {
            return false;
        }
        $brand            = app('antares.memory')->make('primary')->get('brand.default');
        $fieldsCollection = app('antares.customfields.model.view')->select(['id','name','type','label','name','description'])->where('namespace', $eventName)->where('brand_id', $brand)->get();
        $this->putValues($model, $fieldsCollection, $eventName);
        $form->extend(function (FormGrid $form) use($fieldsCollection, $model) {

            $fieldsCollection->each(function($item) use($form, $model) {

                $form->fieldset($item->label, function (Fieldset $fieldset) use($item, $model) {

                    $params = $this->getFieldParams($item);

                    $value      = isset($this->values[$item->id]) ? $this->values[$item->id] : false;
                    $attributes = isset($this->values[$item->id]) && $item->type == 'checkbox' ? ['checked' => 'checked'] : [];

                    $control = ($this->hasMultiOption($item)) ?
                            $this->buildMultiOptionControl($fieldset, $model, $item, $params) :
                            $fieldset->control($params['type'], $item->name)->label($item->label)
                                    ->options($params['options'])
                                    ->attributes($attributes);
                    if ($value) {
                        $control->value($value);
                    }

                    $control->help = $item->description;
                });
            });
        });
    }

    /**
     * building multi option form container
     * @param Fieldset $fieldset
     * @param ArrayAccess $model
     * @param FieldView $item
     * @param array $params
     * @return Fieldset
     */
    protected function buildMultiOptionControl(&$fieldset, ArrayAccess $model, FieldView $item, array $params)
    {
        return $fieldset->control('div', $item->label)->field(function() use($params, $fieldset, $item, $model) {
                    $controls = [];
                    foreach ($params['options'] as $value => $option) {
                        $attributes = [
                            'id' => "{$item->name}{$item->id}",
                        ];
                        if (isset($this->values[$item->id]) && in_array($value, $this->values[$item->id])) {
                            $attributes['checked'] = 'checked';
                        }

                        $control = $fieldset
                                ->control($params['type'], $params['name'])
                                ->value($value)
                                ->label($option)
                                ->attributes($attributes);


                        array_push($controls, $control);
                    }
                    return view('antares/foundation::customfields.forms._multi', ['row' => $model, 'controls' => $controls, 'name' => $params['name'], 'type' => $params['type']]);
                });
    }

    /**
     * getting field params
     * @param \Illuminate\Database\Eloquent\Model $item
     * @return array
     */
    protected function getFieldParams($item)
    {
        $options     = !is_null($item->options) ? explode(';', $item->options) : [];
        $formOptions = [];
        if (!empty($options)) {
            foreach ($options as $option) {
                $tOption                     = explode(':', $option);
                $formOptions[head($tOption)] = last($tOption);
            }
        }
        $type = !is_null($item->type) ? implode(':', [$item->type_name, $item->type]) : $item->type_name;
        $name = count($formOptions) > 1 ? "{$item->name}[]" : $item->name;
        return [
            'options' => $formOptions,
            'type'    => $type,
            'name'    => $name
        ];
    }

    /**
     * get values attached to repository
     * model represents repository which can be connected with customfields
     * @param Eloquent $model
     * @param Collection $collection
     * @return array
     */
    protected function putValues($model, $collection, $eventName)
    {

        $oldRequest = $this->request->old();
        if ($model instanceof Fluent OR ! empty($oldRequest) OR $collection->isEmpty()) {
            return [];
        }

        $fields = [];

        $collection->each(function($current) use(&$fields) {
            $fields[] = $current->id;
        });
        $where = [
            'namespace' => $eventName,
        ];
        if ($model->exists) {
            array_set($where, 'foreign_id', $model->id);
        }

        try {
            $attached = $model->fields()->where($where)->whereIn('field_id', $fields)->get();
        } catch (\BadMethodCallException $ex) {
            Log::alert($ex);
            $attached = $this->fieldData->query()->where($where)->whereIn('field_id', $fields)->get();
        }

        if ($attached->isEmpty()) {
            $this->values = [];
            return;
        }
        $return = [];
        foreach ($attached as $item) {
            if (!is_null($item->option_id)) {
                $return[$item->field_id][] = $item->option_id;
            } else {
                $return[$item->field_id] = $item->data;
            }
        }

        $this->values = $return;
    }

    /**
     * checks whether form element has multi options
     * @param \Illuminate\Database\Eloquent\Model $item
     * @return boolean
     */
    protected function hasMultiOption($item)
    {
        return in_array($item->type, ['radio', 'checkbox']) && count(explode(';', $item->options)) > 1;
    }

}
