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

namespace Antares\Customfields\Console;

use Antares\Customfields\Model\FieldValidatorConfig;
use Antares\Customfields\Model\FieldTypeOption;
use Antares\Customfields\Model\FieldValidator;
use Antares\Customfields\Model\FieldCategory;
use Antares\Customfields\Model\FieldGroup;
use Antares\Customfields\Model\FieldType;
use Antares\Customfields\Model\Field;
use Antares\Customfield\CustomField;
use Illuminate\Support\Facades\DB;
use Antares\View\Console\Command;
use Antares\Brands\Model\Brands;
use Exception;
use Closure;

class CustomfieldSync extends Command
{

    /**
     * human readable command name
     *
     * @var String
     */
    protected $title = 'Customfields Sync';

    /**
     * when command should be executed
     *
     * @var String
     */
    protected $launched = 'daily';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'customfields:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes customfields available in app.';

    /**
     * whether command can be disabled
     *
     * @var boolean 
     */
    protected $disablable = false;

    /**
     * Brands container
     *
     * @var \Illuminate\Support\Collection 
     */
    protected $brands = [];

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->brands = app('antares.brand')->all();
    }

    /**
     * Gets field group instance
     * 
     * @param String $classname
     * @return FieldGroup
     */
    protected function getGroup($classname)
    {
        $category = FieldCategory::query()->firstOrCreate([
            'name' => strtolower(last(explode('\\', $classname)))
        ]);
        return FieldGroup::query()->firstOrCreate([
                    'category_id' => $category->id,
                    'name'        => $category->name,
        ]);
    }

    /**
     * Gets field type
     * 
     * @param CustomField $field
     * @return FieldType
     */
    protected function getFieldType(CustomField $field)
    {
        $type  = explode(':', $field->type);
        $where = ['name' => head($type)];
        if (count($type) > 1) {
            $where['type'] = last($type);
        }
        return FieldType::query()->where($where)->firstOrFail(['id']);
    }

    /**
     * Saves fields
     * 
     * @param FieldGroup $group
     * @param array $fields
     * @return boolean
     */
    protected function saveFields(FieldGroup $group, array $fields = [])
    {
        if (empty($fields)) {
            return false;
        }
        foreach ($fields as $field) {
            $type = $this->getFieldType($field);

            $insert = [
                'type_id'  => $type->id,
                'group_id' => $group->id
            ];

            foreach ($this->brands as $brand) {
                $this->saveField($brand, $field, $insert);
            }
        }
    }

    /**
     * Saves single field
     * 
     * @param Brands $brand
     * @param CustomField $field
     * @param array $insert
     */
    protected function saveField(Brands $brand, CustomField $field, array $insert = [])
    {
        if (Field::query()->where(array_merge($insert, ['brand_id' => $brand->id, 'name' => $field->name,]))->first() !== null) {
            return;
        }
        $customfield = Field::query()->firstOrCreate(array_merge($insert, [
            'brand_id'      => $brand->id,
            'name'          => $field->name,
            'label'         => $field->label,
            'imported'      => 1,
            'force_display' => (int) $field->formAutoDisplay()
        ]));

        $this->saveRules($field, $customfield);
        $this->saveOptions($field, $customfield);
        return $customfield;
    }

    /**
     * Saves custom field rules
     * 
     * @param CustomField $field
     * @param Field $customfield
     * @return boolean
     */
    protected function saveRules(CustomField $field, Field $customfield)
    {
        $rules = array_get($field->getRules(), $field->name, []);
        foreach ($rules as $rule) {
            $validator = FieldValidator::query()->where(['name' => $rule])->first();
            $config    = [
                'field_id' => $customfield->id
            ];
            if (is_null($validator)) {
                $validator       = FieldValidator::query()->where(['name' => 'custom'])->firstOrFail();
                $config['value'] = $rule;
            }
            array_set($config, 'validator_id', $validator->id);
            FieldValidatorConfig::query()->firstOrNew($config)->save();
        }
        return true;
    }

    /**
     * Saves customfield options 
     * 
     * @param CustomField $field
     * @param Field $customfield
     * @return boolean
     */
    protected function saveOptions(CustomField $field, Field $customfield)
    {
        if (is_null($field->options)) {
            return false;
        }
        $options = ($field->options instanceof Closure) ? call_user_func($field->options) : $field->options;
        foreach ($options as $value => $label) {
            FieldTypeOption::query()->firstOrCreate([
                'field_id' => $customfield->id,
                'label'    => $label,
                'value'    => $value
            ]);
        }
        return true;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $customfields = app('customfields')->get();

        DB::beginTransaction();
        try {
            foreach ($customfields as $model => $customfield) {
                $fields = !is_array($customfield) ? [$customfield] : $customfield;
                $group  = $this->getGroup($model);
                $this->saveFields($group, array_where($fields, function($field, $index) {
                            return $field->configurable();
                        }));
            }
        } catch (Exception $ex) {
            vdump($ex);
            exit;
            DB::rollback();
        }
        DB::commit();
    }

}
