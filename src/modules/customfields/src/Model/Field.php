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

namespace Antares\Customfields\Model;

use Antares\Brands\Model\BrandableTrait;
use Antares\Logger\Traits\LogRecorder;
use Antares\Model\Eloquent;

class Field extends Eloquent
{

    use BrandableTrait,
        LogRecorder;

// Disables the log record in this model.
    protected $auditEnabled   = true;
// Disables the log record after 500 records.
    protected $historyLimit   = 500;
// Fields you do NOT want to register.
    protected $dontKeepLogOf  = ['created_at', 'updated_at', 'deleted_at'];
// Tell what actions you want to audit.
    protected $auditableTypes = ['created', 'saved', 'deleted'];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_fields';

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass = 'Field';

    /**
     * fillable array
     * 
     * @var array
     */
    public $fillable = [
        'brand_id', 'group_id', 'type_id', 'name', 'label', 'placeholder', 'value', 'description', 'imported', 'force_display', 'additional_attributes'
    ];

    /**
     * Belongs to relationship with Validator.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function validators()
    {
        return $this->hasMany('Antares\Customfields\Model\FieldValidatorConfig', 'field_id');
    }

    /**
     * Belongs to relationship with Groups.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groups()
    {
        return $this->hasOne('Antares\Customfields\Model\FieldGroup', 'id', 'group_id');
    }

    /**
     * Belongs to relationship with Categories.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categories()
    {
        return $this->hasOne('Antares\Customfields\Model\FieldCategory', 'id');
    }

    /**
     * Belongs to relationship with Types.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function types()
    {
        return $this->hasOne('Antares\Customfields\Model\FieldType', 'id', 'type_id');
    }

    /**
     * Relation to Fieldsets
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function fieldsets()
    {
        return $this->belongsToMany(Fieldsets::class, 'tbl_field_fieldsets', 'field_id', 'fieldset_id');
    }

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery()->where('brand_id', brand_id());
    }

    /**
     * get flatten validators attached to field
     * @return array
     */
    public function getFlattenValidators()
    {
        if (!$this->exists) {
            return [];
        }

        $validators = $this->validators;
        $return     = [];
        if (!empty($validators)) {
            foreach ($validators as $validator) {
                $return[$validator->validator_id] = $validator->value;
            }
        }
        return $return;
    }

    /**
     * Belongs to relationship with Options.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function options()
    {
        return $this->hasMany('Antares\Customfields\Model\FieldTypeOption', 'field_id', 'id');
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::customfields/{id}/edit');
    }

    /**
     * Gets log title
     * 
     * @param mixed $id
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return String
     */
    public static function getLogTitle($id, $model)
    {
        $keys  = ['FIELD', strtoupper($model->type)];
        $html  = app('html');
        $data  = $model->toArray();
        $name  = array_get($data, 'new_value.name', array_get($data, 'old_value.name'));
        $label = array_get($data, 'new_value.label', array_get($data, 'old_value.label'));

        $params  = [
            'owner_id' => $model->type == 'deleted' ? $name : $html->link(handles('antares/customfields::/' . $id . '/edit'), $name),
            'user'     => $html->link(handles('antares::foundation/users/' . $model->user->id), '#' . $model->user->id . ' ' . $model->user->fullname),
            'label'    => $label
        ];
        if (!empty($related = array_get($data, 'related_data'))) {
            if (!empty($type = current(array_get($related, 'types', [])))) {
                array_push($keys, 'TYPE');
                array_set($params, 'type', implode(' ', array_only($type, ['name', 'type'])));
            }
            if (!empty($category = current(array_get($related, 'categories', [])))) {
                array_push($keys, 'CATEGORY');
                array_set($params, 'category', array_get($category, 'name'));
            }
            if (!empty($group = current(array_get($related, 'groups', [])))) {
                array_push($keys, 'GROUP');
                array_set($params, 'group', array_get($group, 'name'));
            }
        }


        return trans('antares/customfields::operations.' . implode('_', $keys), $params);
    }

}
