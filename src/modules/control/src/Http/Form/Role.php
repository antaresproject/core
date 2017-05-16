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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Control\Http\Form;

use Illuminate\Database\Eloquent\Model;
use Antares\Html\Form\FormBuilder;
use Antares\Html\Form\Fieldset;
use Antares\Html\Form\Grid;

class Role extends FormBuilder
{

    /**
     * form rules container
     *
     * @var array
     */
    protected $rules = [
        'name'        => ['required', 'min:3', 'max:255'],
        'description' => ['required', 'min:10', 'max:4000'],
    ];

    /**
     * constructing
     * 
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $grid = app(Grid::class);
        $grid->name('role');
        $grid->resourced('antares::control/roles', $model);
        $grid->hidden('id');

        $grid->fieldset(function (Fieldset $fieldset) {
            $fieldset->legend(trans('antares/control::messages.group_details'));
            $fieldset->control('input:text', 'name')
                    ->attributes(['required' => 'required'])
                    ->label(trans('antares/control::label.role_name'))
                    ->wrapper(['class' => 'w220']);
        });
        if (!$model->exists) {
            publish('control', ['js/roles-form.js']);

            $grid->fieldset(function (Fieldset $fieldset) {

                $roles = app('antares.role')->managers()->pluck('name', 'id')->toArray();

                $fieldset->control('checkbox', 'import')
                        ->attributes(['class' => 'role-selector', 'data-icheck' => "true"])
                        ->label(trans('antares/control::label.import_configuration'))
                        ->value(1);


                $fieldset->control('select', 'roles')
                        ->attributes(['data-selectAR' => true])
                        ->label(trans('antares/control::label.select_parent_role'))
                        ->options($roles)
                        ->wrapper(['class' => 'form-block roles-select-container hidden w570']);
            });
        }


        $grid->fieldset(function (Fieldset $fieldset) use($model) {
            $fieldset->control('textarea', 'description')
                    ->attributes(['required' => 'required', 'cols' => '5', 'rows' => '5', 'class' => 'as-fs'])
                    ->label(trans('antares/control::label.role_description'));


            $fieldset->control('button', 'cancel')
                    ->field(function() {
                        return app('html')->link(handles("antares::control/index/roles"), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                    });

            $fieldset->control('button', 'button')
                    ->attributes(['type' => 'submit', 'class' => 'btn btn--submit btn--md btn--primary mdl-button mdl-jsb mdl-re'])
                    ->value(trans('antares/foundation::label.save_changes'));
        });

        $grid->fieldset(function (Fieldset $fieldset) use($model) {
            $fieldset->legend(trans('antares/control::messages.group_area'));

            $fieldset->control('select', 'area')
                    ->attributes(['data-selectAR' => true])
                    ->label(trans('antares/control::label.select_level'))
                    ->options(function() {
                        return array_merge(config('areas.areas'), [config('antares/foundation::handles') => config('antares/foundation::application.name')]);
                    })
                    ->wrapper(['class' => 'w180']);
        });

        $rules         = $this->rules;
        $rules['name'] = array_merge($rules['name'], ['unique:tbl_roles,name' . ((!$model->exists) ? '' : ',' . $model->id)]);
        $grid->rules($rules);

        parent::__construct($grid);
    }

}
