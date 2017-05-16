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

namespace Antares\Customfields\Http\Forms;

use Antares\Contracts\Html\Form\Grid as FormGrid;
use Antares\Html\Form\Factory as FormFactory;
use Antares\Customfields\Model\FieldType;
use Antares\Contracts\Html\Form\Fieldset;
use Illuminate\Support\Facades\URL;

class FieldFormFactory extends FormFactory
{

    /**
     * Model instance
     *
     * @var \Illuminate\Database\Eloquent\Model 
     */
    protected $model;

    /**
     * @todo refaktoryzacja
     * @param type $form
     * @param array $prepared
     */
    protected function setGroupsContainer(&$form, array $prepared = null)
    {
        $form->name('Customfields form');
        /** selectors */
        $form->fieldset(trans('antares/customfields::label.container'), function (Fieldset $fieldset) use($prepared) {

            $attributes = [];
            if ($prepared['imported']) {
                array_set($attributes, 'disabled', 'disabled');
            }
            /** category list * */
            $category = $fieldset->control('select', 'category')
                    ->options($prepared['categoryOptions'])
                    ->attributes(['id' => 'FieldCategorySelector'] + $attributes)
                    ->value($prepared['categoryId'])
                    ->label(trans('antares/customfields::label.category'))
                    ->wrapper(['class' => 'w370']);
            if ($prepared['imported']) {
                $category->help = trans('* Change category is disabled for native customfields.');
            }


            /** group list * */
            $group = $fieldset
                    ->control('select', 'group')
                    ->options($prepared['groupOptions'])
                    ->attributes(['id' => 'FieldGroupSelector'] + $attributes)
                    ->value($prepared['groupId'])
                    ->label(trans('antares/customfields::label.group'))
                    ->wrapper(['class' => 'w370']);
            if ($prepared['imported']) {
                $group->help = trans('* Change group is disabled for native customfields.');
            }

            /** type list * */
            $type = $fieldset->control('select', 'type')
                    ->options($prepared['typeOptions'])
                    ->value($prepared['typeId'])
                    ->attributes(['id' => 'FieldTypeSelector'] + $attributes)
                    ->label(trans('antares/customfields::label.type'))
                    ->wrapper(['class' => 'w370']);

            if ($prepared['imported']) {
                $type->help = trans('* Change type is disabled for native customfields.');
            }
            $fieldset->control('select', 'fieldset[]')
                            ->label(trans('antares/customfields::label.assigned_fieldset'))
                            ->options(function() {
                                return \Antares\Customfields\Model\Fieldsets::pluck('name', 'name');
                            })
                            ->wrapper(['class' => 'w500'])
                            ->attributes(['multiple' => 'multiple', 'data-selectar' => false, 'id' => 'fieldset'])
                            ->value(function($row) {
                                return $row->fieldsets->pluck('name')->toArray();
                            })->help = trans('* Name of groups where field is assigned to. Start typing to create new fieldset.');
        });
    }

    /**
     * @todo refaktoryzacja
     * @param type $form
     * @param array $prepared
     */
    protected function setValidatorsContainer(&$form, array $prepared = null)
    {
        if (!$prepared['availableValidators']->isEmpty()) {
            $fieldset = $form->fieldset(trans('antares/customfields::label.validators'), function (Fieldset $fieldset) use($prepared) {
                foreach ($prepared['availableValidators'] as $validator) {
                    if (!$this->model->exists && $validator->validator->name == 'custom') {
                        continue;
                    }
                    if (!$this->model->imported && $validator->validator->name == 'custom') {
                        continue;
                    }
                    $label = ($validator->validator->name == 'custom' && isset($prepared['activeValidators'][$validator->validator->id])) ? $prepared['activeValidators'][$validator->validator->id] : "antares/customfields::validator.{$validator->validator->name}";

                    $attributes = ['id' => "{$validator->validator->name}Validator"];
                    if ($validator->validator->name == 'custom' && isset($prepared['activeValidators'][$validator->validator->id])) {
                        $attributes['disabled'] = 'disabled';
                        $label                  = "antares/customfields::validator." . array_get($prepared, 'activeValidators.' . $validator->validator->id, 'default_custom_validator_label');
                    }
                    $shouldBeChecked = array_key_exists($validator->validator->id, $prepared['activeValidators']);
                    if (!$shouldBeChecked and $validator->validator->name == 'custom') {
                        continue;
                    }
                    $control = $fieldset
                            ->control('input:checkbox', "validators[]")
                            ->attributes($attributes)
                            ->value($validator->validator->id)
                            ->label(trans($label));


                    if ($validator->validator->description) {
                        $control->help = $validator->validator->description;
                    }


                    if ($shouldBeChecked) {
                        $control->attributes = ['checked' => 'checked'];
                    }

                    if ($validator->validator->customizable) {
                        $customValidator = $fieldset
                                ->control('input:text', "validator_custom[{$validator->validator->id}]")
                                ->attributes(['class' => 'w470'])
                                ->label(trans('antares/customfields::label.custom_validator_value'));
                        if (isset($prepared['activeValidators'][$validator->validator->id]) && !empty($prepared['activeValidators'][$validator->validator->id])) {
                            $customValidator->value($prepared['activeValidators'][$validator->validator->id]);
                        } else {
                            $customValidator->value($validator->validator->default);
                        }
                    }
                }
            });
        }
    }

    /**
     * names container
     * 
     * @param FieldFormFactory $form
     */
    protected function setNamesContainer(&$form)
    {
        $shouldContainValueField = false;
        foreach ($form->fieldsets as $fieldset) {
            $shouldContainValueField = $this->shouldContainValueField(collect($fieldset->controls)->where('name', 'type')->first()->value);
        }
        $form->fieldset(trans('antares/customfields::label.options'), function (Fieldset $fieldset) use($shouldContainValueField) {

            $attributes = ['class' => 'w470'];
            if ($this->model->imported) {
                $attributes['disabled'] = 'disabled';
            }
            $nameField = $fieldset->control('input:text', 'name')
                    ->label(trans('antares/customfields::label.name'))
                    ->attributes($attributes);

            if ($this->model->imported) {
                $nameField->help = trans('antares/customfields::messages.customfield_name_edit_disabled_for_native');
            }


            $fieldset->control('input:text', 'label')->label(trans('antares/customfields::label.default_label'))->attributes(['class' => 'w470']);

            $fieldset->control('input:text', 'placeholder')
                    ->label(trans('antares/customfields::label.default_placeholder'))
                    ->attributes(['class' => 'w470']);

            $fieldset->control('textarea', 'description')
                    ->label(trans('antares/customfields::label.description'))
                    ->attributes(['class' => 'w570', 'cols' => '5', 'rows' => '5']);

            if ($shouldContainValueField) {
                $fieldset->control('input:text', 'value')
                        ->label(trans('antares/customfields::label.value'))
                        ->attributes(['class' => 'w470']);
            }
            $control = $fieldset->control('input:checkbox', 'force_display')
                    ->label(trans('antares/customfields::label.force_display_on_form'))
                    ->value(1);
            if ($this->model->force_display) {
                $control->checked();
            }

            $fieldset->control('input:text', 'additional_attributes')
                    ->label(trans('antares/customfields::label.field_attributes'))
                    ->attributes(['class' => 'w500'])
                    ->help(trans('antares/customfields::label.field_attributes_help'));
        });
    }

    /**
     * Whether field should contain value input
     * 
     * @param mixed $typeId
     * @return boolean
     */
    protected function shouldContainValueField($typeId = null)
    {
        if (is_null($typeId)) {
            return false;
        }
        $model = FieldType::query()->findOrFail($typeId);
        return $model->name == 'select' or ( $model->name == 'input' and in_array($model->type, ['radio', 'checkbox']));
    }

    /**
     * buttons container
     * 
     * @param FieldFormFactory $form
     */
    protected function setButtonsContainer(&$form)
    {
        $form->fieldset('', function (Fieldset $fieldset) {
            $fieldset->control('button', 'cancel-button')
                    ->attributes(['type' => 'submit', 'class' => 'btn btn--md btn--default mdl-button mdl-js-button mdl-js-ripple-effect'])
                    ->value(trans('antares/foundation::label.cancel'));

            $fieldset->control('button', 'save-button')
                    ->attributes(['type' => 'submit'])
                    ->value(trans('antares/foundation::label.save'));
        });
    }

    /**
     * @todo refaktoryzacja
     * @param FormFactory $form
     * @param array $prepared
     */
    protected function setMultiDataContainer(&$form, array $prepared = null)
    {
        $options = isset($prepared['options']) && !empty($prepared['options']) ? $prepared['options'] : [];
        if (isset($prepared['multi']) && (int) $prepared['multi'] > 0) {
            $form->fieldset(trans('antares/customfields::label.data_options'), function (Fieldset $fieldset) use($options) {
                $fieldset->control('input:text', '')
                        ->field(function () {
                            return '<button type="button" class="btn btn--s-small btn--primary mdl-button mdl-js-button mdl-js-ripple-effect addButton">' . trans('antares/customfields::label.button_add_option_value') . '</button>';
                        });
                if ($options->isEmpty()) {

                    $fieldset->control('input:text', 'option_label[]')
                            ->attributes(['class' => 'w470'])
                            ->label(trans('antares/customfields::label.multi_option_label'));

                    $fieldset->control('input:text', 'option[]')
                            ->attributes(['class' => 'w470'])
                            ->label(trans('antares/customfields::label.multi_option_value'));
                }
            });
            foreach ($options as $option) {
                $form->fieldset(trans('antares/customfields::label.data_options'), function (Fieldset $fieldset) use($option) {
                    $fieldset->attributes(['class' => 'group-options']);

                    $fieldset->control('input:text', "option_label[{$option->id}]")
                            ->attributes(['class' => 'w470'])
                            ->value($option->label)
                            ->label(trans('antares/customfields::label.multi_option_label'));

                    $fieldset->control('input:text', "option[{$option->id}]")
                            ->attributes(['class' => 'w470'])
                            ->value($option->value)
                            ->label(trans('antares/customfields::label.multi_option_value'));
                    $fieldset->control('input:text', '')
                            ->field(function () {
                                return '<button type="button" class="btn btn--s-small btn--primary mdl-button mdl-js-button mdl-js-ripple-effect removeButton">Remove</button>';
                            });
                });
            }

            $form->fieldset(trans('antares/customfields::label.data_options'), function (Fieldset $fieldset) {
                $fieldset->attributes(['class' => 'group-options hide', 'id' => 'optionTemplate']);
                $fieldset->control('input:text', 'option_label[]')
                        ->label(trans('antares/customfields::label.multi_option_label'))
                        ->attributes(['class' => 'w470']);
                $fieldset->control('input:text', 'option[]')
                        ->label(trans('antares/customfields::label.multi_option_value'))
                        ->attributes(['class' => 'w470']);
                $fieldset->control('input:text', '')
                        ->field(function () {
                            return '<button type="button" class="btn btn--s-small btn--primary mdl-button mdl-js-button mdl-js-ripple-effect removeButton">Remove</button>';
                        });
            });
        }
    }

    /**
     * @param type $presenter
     * @param type $model
     * @param type $prepared
     * @return type
     */
    public function build($presenter, $model, $prepared)
    {
        $this->model = $model;
        $return      = $this->app->make('antares.form')->of('antares.customfields.model', function (FormGrid $form) use ($model, $prepared, $presenter) {

            $form->resource($presenter, 'antares/foundation::customfields', $model, ['id' => 'surveyForm', 'class' => 'form--hor']);

            $form->hidden('id');
            $form->hidden('path', function($field) use($model) {
                $attributes   = ($model->exists) ? ['customfield' => $model->id] : [];
                $field->value = ($model->exists) ? URL::route('customfield.base', $attributes, false) : URL::route('customfield.create', [], false);
            });
            $prepared['imported'] = $model->imported;
            $this->setGroupsContainer($form, $prepared);
            $this->setNamesContainer($form);
            $this->setMultiDataContainer($form, $prepared);
            $this->setValidatorsContainer($form, $prepared);

            $this->setButtonsContainer($form);
            $form->layout('antares/customfields::admin.form');
        });
        return $return;
    }

}
