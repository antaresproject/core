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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Foundation\Http\Form;

use Illuminate\Contracts\Container\Container;
use Antares\Contracts\Html\Form\Presenter;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Html\Form\Grid as HtmlGrid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Antares\Html\Form\ClientScript;
use Antares\Html\Form\FormBuilder;

class User extends FormBuilder implements Presenter
{

    /**
     * form validation rules
     *
     * @var array
     */
    protected $rules = [
        'email'     => ['required', 'email'],
        'fullname'  => ['required'],
        'roles'     => ['required'],
        'radios[]'  => ['required'],
        'options[]' => ['required']
    ];

    /**
     * constructing
     * 
     * @param Model $model
     */
    public function __construct(Model $model)
    {

        parent::__construct(app(HtmlGrid::class), app(ClientScript::class), app(Container::class));
        Event::fire('antares.forms', 'users.register');
        $this->grid->name('User form');
        $this->grid->resource($this, 'antares/foundation::users', $model);
        $this->grid->hidden('id');
        $this->fieldset();
        $this->grid->rules($this->rules);
        $this->grid->ajaxable();
    }

    /**
     * form fieldset
     * 
     * @return Fieldset
     */
    protected function fieldset()
    {

        return $this->grid->fieldset('user-fieldset', function (Fieldset $fieldset) {
                    $fieldset->control('input:text', 'email')
                            ->label(trans('antares/foundation::label.users.email'));

                    $fieldset->control('input:text', 'fullname')
                            ->label(trans('antares/foundation::label.users.fullname'));

                    $fieldset->control('input:password', 'password')
                            ->label(trans('antares/foundation::label.users.password'));

                    $fieldset->control('select', 'roles[]')
                            ->label(trans('antares/foundation::label.users.roles'))
                            ->attributes(['class' => 'w470'])
                            ->options(function () {
                                return app('antares.role')->members()->pluck('name', 'id');
                            })
                            ->value(function ($row) {
                                $roles = [];
                                foreach ($row->roles as $row) {
                                    $roles[] = $row->id;
                                }
                                return $roles;
                            });



                    $fieldset->control('remote_select', 'select')
                            ->label('Remote Select')
                            ->options([
                                ['0' => 'please select option...']
                            ])
                            ->attributes([
                                'id'            => 'select-infinity',
                                'options'       => ['placeholder' => 'Search for a repo ...'],
                                'pluginOptions' => [
                                    'allowClear'              => true,
                                    'minimumInputLength'      => 1,
                                    'minimumResultsForSearch' => 'Infinity',
                                    'ajax'                    => [
                                        'url'      => handles('antares/foundation::users/elements'),
                                        'dataType' => 'json',
                                        'delay'    => 250,
                                        'cache'    => true
                                    ],
                                ],
                    ]);
                    $fieldset->control('button', 'cancel')
                            ->field(function() {
                                return app('html')->link(handles("antares/foundation::users"), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                            });

                    $fieldset->control('button', 'button')
                            ->attributes(['type' => 'submit', 'value' => trans('Submit'), 'class' => 'btn btn--md btn--primary mdl-button mdl-js-button mdl-js-ripple-effect'])
                            ->value(trans('Submit'));
                });
    }

}
