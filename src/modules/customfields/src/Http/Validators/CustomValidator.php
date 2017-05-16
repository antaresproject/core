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



namespace Antares\Customfields\Http\Validators;

use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Input;
use Antares\Support\Facades\Foundation;

class CustomValidator extends Validator
{

    /**
     * checks unique field creation 
     * @param type $attribute
     * @param type $value
     * @param type $parameters
     * @return boolean
     */
    public function validateNameOnCreate($attribute, $value, $parameters)
    {
        $groupId = Input::get('group');
        $typeId  = Input::get('type');
        $exists  = Foundation::make('antares.customfields.model.view')
                ->query()
                ->where('group_id', $groupId)
                ->where('type_id', $typeId)
                ->where('name', $value)
                ->exists();
        if ($exists) {
            $this->setCustomMessages(['name-on-create' => trans('antares/customfields::validator.create.name-non-unique')]);
            $this->addFailure($attribute, 'name-on-create', []);
            return false;
        }


        return true;
    }

    /**
     * validates validator list
     * @param type $attribute
     * @param type $value
     * @param type $parameters
     * @return boolean
     */
    public function validateValidatorList($attribute, $value, $parameters)
    {
        $validators = Input::get('validators');
        if (!is_null($validators)) {
            foreach ($validators as $index => $id) {
                if (isset($value[$id]) && $value[$id] == '') {
                    $this->setCustomMessages(["validator_custom[{$id}]" => trans('antares/customfields::validator.create.validators-empty-fields')]);
                    $this->addFailure("validator_custom[{$id}]", "validator_custom[{$id}]", []);
                    break;
                }
            }
        }

        return true;
    }

    /**
     * validates when checked checkboxes number is less than config
     * @param array $attribute
     * @param array|mixed $value
     * @param array $parameters
     * @return boolean
     */
    public function validateMinChecked($attribute, $value, $parameters)
    {
        $input = Input::get($attribute);

        if (!isset($parameters[0]) OR ! is_numeric($parameters[0])) {
            return true;
        }
        if (count($input) < $parameters[0]) {
            $this->setCustomMessages([$attribute => trans('antares/customfields::validator.checkboxes.min-checked')]);
            $this->addFailure($attribute, $attribute, []);
        }

        return true;
    }

    /**
     * validates when checked checkboxes number is more than config
     * @param array $attribute
     * @param array|mixed $value
     * @param array $parameters
     * @return boolean
     */
    public function validateMaxChecked($attribute, $value, $parameters)
    {
        $input = Input::get($attribute);
        if (!isset($parameters[0]) OR ! is_numeric($parameters[0])) {
            return true;
        }
        if (count($input) > $parameters[0]) {
            $this->setCustomMessages([$attribute => trans('antares/customfields::validator.checkboxes.max-checked')]);
            $this->addFailure($attribute, $attribute, []);
        }
        return true;
    }

}
