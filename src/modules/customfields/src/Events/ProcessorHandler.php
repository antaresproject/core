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

use Illuminate\Support\Facades\Input;
use Antares\Customfields\Model\FieldView;
use Antares\Customfields\Model\FieldData;
use Illuminate\Support\Facades\Log;
use ArrayAccess;

class ProcessorHandler
{

    /**
     * @var Input
     */
    protected $input;

    /**
     * @var FieldView
     */
    protected $field;

    /**
     * @var FieldData
     */
    protected $fieldData;

    /**
     * current user Id
     * @var numeric
     */
    protected $userId;

    /**
     * selected brand id
     * @var numeric
     */
    protected $brandId;

    /**
     * Construct a new config handler.
     *
     * @param Input $input
     * @param FieldView $field
     */
    public function __construct(Input $input, FieldView $field, FieldData $fieldData, $userId = null)
    {
        $this->input     = $input::all();
        $this->field     = $field;
        $this->fieldData = $fieldData;
        $this->userId    = is_null($userId) ? auth()->user()->id : $userId;
    }

    /**
     * saves data values from customfields
     * @param ArrayAccess $parameters
     * @param String $namespace
     * @return boolean
     */
    public function onSave(ArrayAccess $parameters, $namespace = null)
    {

        $exception = false;
        if (is_null($namespace)) {
            return true;
        }
        try {
            $fieldsCollection = $this->field->query()->where('namespace', $namespace)->get();
            if ($fieldsCollection->isEmpty()) {
                return true;
            }
            $collection = [];

            $fieldsCollection->each(function($field) use(&$collection) {

                if (array_key_exists($field->name, $this->input)) {
                    $collection[$field->id] = $this->input[$field->name];
                }
            });
            call_user_func_array([$this, 'save'], [$collection, $parameters->id, $namespace]);
        } catch (\Exception $e) {

            $exception = $e;
        }

        return $exception === false;
    }

    /**
     * saves collection of data from customfields inputs
     * @param array $parameters
     * @param numeric $foreignId
     * @param String $namespace
     * @return boolean
     */
    protected function save(array $parameters, $foreignId, $namespace = null)
    {

        if (empty($parameters)) {
            return true;
        }
        $where = ['user_id' => $this->userId, 'foreign_id' => $foreignId, 'namespace' => $namespace];

        foreach ($parameters as $fieldId => $fieldValue) {
            array_set($where, 'field_id', $fieldId);

            if ($this->isTabular($fieldValue)) {
                foreach ($fieldValue as $optionId) {
                    array_set($where, 'option_id', $optionId);
                    $model = $this->resolveModel($where);
                    $model->save();
                }
            } else {
                $model       = $this->resolveModel($where);
                $model->data = $fieldValue;
                $model->save();
            }
        }
    }

    /**
     * resolve model instance
     * @param array $where
     * @return Eloquent
     */
    protected function resolveModel(array $where)
    {
        $model = $this->fieldData->query()->where($where)->first();
        return (is_null($model)) ? $this->fieldData->newInstance($where) : $model;
    }

    /**
     * is input value is like a tabular data
     * @param array | mixed $data
     * @return boolean
     */
    protected function isTabular($data = null)
    {
        return is_array($data);
    }

}
