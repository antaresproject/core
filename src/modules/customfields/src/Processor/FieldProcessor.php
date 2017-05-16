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



namespace Antares\Customfields\Processor;

use Antares\Customfields\Http\Validators\FieldValidator as Validator;
use Antares\Customfields\Http\Presenters\FieldPresenter as Presenter;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Antares\Customfields\Http\Validators\CustomValidator;
use Antares\Customfields\Console\CustomfieldSync;
use Antares\Customfields\Contracts\FieldCreator;
use Antares\Customfields\Contracts\FieldUpdater;
use Antares\Customfields\Contracts\FieldRemover;
use Antares\Customfields\Model\FieldFieldsets;
use Antares\Foundation\Processor\Processor;
use Antares\Customfields\Model\Fieldsets;
use Illuminate\Support\Facades\Response;
use Antares\Support\Facades\Foundation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Route;
use Antares\Model\Eloquent;

class FieldProcessor extends Processor
{

    /**
     * constructing
     * 
     * @param Presenter $presenter
     * @param Validator $validator
     */
    public function __construct(Presenter $presenter, Validator $validator)
    {
        $this->presenter = $presenter;
        $this->validator = $validator;
    }

    /**
     * shows list of elements
     * 
     * @return \Illuminate\View\View
     */
    public function show()
    {
        if (!request()->ajax()) {
            app(CustomfieldSync::class)->handle();
        }

        return $this->presenter->table();
    }

    /**
     * creating new custom field form
     * @param CustomfieldCreator $listener
     */
    public function create(FieldCreator $listener, Route $route)
    {
        $eloquent = Foundation::make('antares.customfields.model');
        $form     = $this->presenter->form($eloquent, 'create', $route);
        $this->fireEvent('form', [$eloquent, $form]);
        return $listener->showFieldCreator(compact('eloquent', 'form'));
    }

    /**
     * storing custom field in db
     * @param CustomfieldCreator $listener
     * @param array $input
     * @return redirect|mixed
     */
    public function store(FieldCreator $listener, array $input)
    {
        $this->attachValidator();
        $validation = $this->validator->on('create')->with($input);
        if ($validation->fails()) {
            if (app('request')->ajax()) {
                return Response::json($validation->getMessageBag()->getMessages(), 200);
            }
            return $listener->createFieldFailedValidation($validation->getMessageBag());
        }

        $customfield = Foundation::make('antares.customfields.model');

        try {
            $this->saving($customfield, $input, 'create');
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->createFieldFailed(['error' => $e->getMessage()]);
        }

        return $listener->fieldCreated();
    }

    /**
     * attach custom validation to custom field form
     * 
     * @return mixed
     */
    protected function attachValidator()
    {
        return ValidatorFacade::resolver(function($translator, $data, $rules, $messages) {
                    return new CustomValidator($translator, $data, $rules, $messages);
                });
    }

    /**
     * Saves fieldsets
     * 
     * @return boolean
     */
    protected function saveFieldsets($model)
    {
        $fieldsets = input('fieldset', []);
        if (empty($fieldsets)) {
            return false;
        }
        foreach ($fieldsets as $name) {
            $fieldset = Fieldsets::query()->firstOrCreate(['name' => $name]);
            FieldFieldsets::query()->firstOrCreate([
                'fieldset_id' => $fieldset->id,
                'field_id'    => $model->id
            ]);
        }
    }

    /**
     * saving custom field in db
     * @param \Antares\Customfields\Http\Processors\Eloquent $model
     * @param array $input
     * @param String $type
     * @return boolean
     */
    protected function saving(Eloquent $model, $input = [], $type = 'create')
    {
        $beforeEvent = ($type === 'create' ? 'creating' : 'updating');
        $afterEvent  = ($type === 'create' ? 'created' : 'updated');


        $this->fireEvent($beforeEvent, [$model]);
        $this->fireEvent('saving', [$model]);
        $attributes = [
            'label'       => $input['label'],
            'description' => $input['description'],
            'placeholder' => array_get($input, 'placeholder'),
            'value'       => array_get($input, 'value'),
        ];

        if (!is_null($name = array_get($input, 'name'))) {
            array_set($attributes, 'name', $name);
        }
        if (!is_null($name = array_get($input, 'name'))) {
            array_set($attributes, 'name', $name);
        }
        if (isset($input['group'])) {
            $attributes['group_id'] = $input['group'];
        }
        if (isset($input['type'])) {
            $attributes['type_id'] = $input['type'];
        }
        array_set($attributes, 'force_display', (int) isset($input['force_display']));
        if (isset($input['additional_attributes'])) {
            $attributes['additional_attributes'] = $input['additional_attributes'];
        }
        $model->fill($attributes);
        DB::transaction(function () use ($model, $input) {
            if ($model->exists) {
                $model->validators()->delete();
            }
            $model->save();
            $this->saveFieldsets($model);
            $fieldId = $model->id;

            if (isset($input['validators'])) {

                foreach ($input['validators'] as $validatorId) {
                    $configuration = Foundation::make('antares.customfields.model.validator.config')->newInstance();
                    $attribs       = [
                        'field_id'     => $fieldId,
                        'validator_id' => $validatorId,
                        'value'        => isset($input['validator_custom'][$validatorId]) ? $input['validator_custom'][$validatorId] : null
                    ];
                    $configuration->fill($attribs);
                    $configuration->save();
                }
            }
            if (isset($input['option'])) {
                $optionFoundation = Foundation::make('antares.customfields.model.type.option');
                $keys             = [];
                foreach ($input['option'] as $index => $optionValue) {
                    if (strlen($optionValue) > 0) {
                        if ($model->exists) {
                            $option = $optionFoundation::firstOrNew([
                                        'label'    => $input['option_label'][$index],
                                        'value'    => $optionValue,
                                        'field_id' => $fieldId
                            ]);
                        } else {
                            $option = $optionFoundation->newInstance();
                        }
                        $attribs = [
                            'field_id' => $fieldId,
                            'label'    => $input['option_label'][$index],
                            'value'    => $optionValue
                        ];
                        $option->fill($attribs);
                        $option->save();
                        $keys[]  = $optionValue;
                    }
                }
                foreach ($model->options as $option) {
                    if (!in_array($option->value, $keys)) {
                        $option->delete();
                    }
                }
            }
        });

        $this->fireEvent($afterEvent, [$model]);
        $this->fireEvent('saved', [$model]);

        return true;
    }

    /**
     * updates custom field
     * @param CustomfieldUpdater $listener
     * @param type $id
     */
    public function update(FieldUpdater $listener, $id, array $input)
    {
        $customfield = Foundation::make('antares.customfields.model')->findOrFail($id);
        $this->attachValidator();
        $on          = $customfield->imported ? 'imported' : 'update';
        $validation  = $this->validator->on($on)->with($input);

        if ($validation->fails()) {
            return $listener->updateFieldFailedValidation($validation->getMessageBag(), $id);
        }


        try {
            $this->saving($customfield, $input, 'update');
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->updateFieldFailed(['error' => $e->getMessage()]);
        }

        return $listener->fieldUpdated();
    }

    /**
     * edit customfield
     * @param CustomfieldUpdater $listener
     * @param numeric $id
     * @return redirect|mixed
     */
    public function edit(FieldUpdater $listener, $id, Route $route)
    {
        $eloquent = null;
        try {
            $eloquent = Foundation::make('antares.customfields.model')->findOrFail($id);
        } catch (\Exception $ex) {
            Log::emergency($ex);
            return $listener->abortWhenFieldMismatched();
        }
        try {
            $form = $this->presenter->form($eloquent, 'update', $route);
            $this->fireEvent('form', [$eloquent, $form]);
            return $listener->showFieldUpdater(compact('eloquent', 'form'));
        } catch (\Exception $ex) {
            Log::emergency($ex);
            return $listener->updateFieldFailed(['error' => $ex->getMessage()]);
        }
    }

    /**
     * destroying custom field
     * @param CustomfieldRemover $listener
     * @param numeric $id
     * @return mixed
     */
    public function destroy(FieldRemover $listener, $id)
    {
        $model = Foundation::make('antares.customfields.model')->findOrFail($id);


        try {
            $this->fireEvent('deleting', [$model]);

            DB::transaction(function () use ($model) {
                $model->delete();
            });
            $this->fireEvent('deleted', [$model]);
        } catch (Exception $e) {
            Log::emergency($ex);
            return $listener->removeFieldFailed(['error' => $e->getMessage()]);
        }

        return $listener->fieldRemoved();
    }

    /**
     * Fire Event related to eloquent process.
     * @param  string  $type
     * @param  array   $parameters
     * @return void
     */
    protected function fireEvent($type, array $parameters = [])
    {
        Event::fire("antares.{ $type}: customfields", $parameters);
    }

}
