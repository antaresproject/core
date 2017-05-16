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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Http\Form;

use Illuminate\Contracts\Container\Container;
use Antares\Contracts\Html\Form\Presenter;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Html\Form\Grid as HtmlGrid;
use Illuminate\Database\Eloquent\Model;
use Antares\Contracts\Html\Form\Grid;
use Antares\Html\Form\ClientScript;
use Antares\Html\Form\FormBuilder;

class DeviceForm extends FormBuilder implements Presenter
{

    /**
     * form validation rules
     *
     * @var array 
     */
    protected $rules = [
        'name' => ['required']
    ];

    /**
     * constructing
     * 
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        parent::__construct(app(HtmlGrid::class), app(ClientScript::class), app(Container::class));
        $this->grid  = $this->setupForm($this->grid);
        $model->name = strlen($model->name) > 0 ? $model->name : $model->machine;
        $this->grid->resource($this, handles('antares::logger/devices'), $model);
        $this->grid->rules([
            'name' => ['required']
        ]);


        $this->grid->fieldset('info', function (Fieldset $fieldset) {

            $fieldset->legend('Device settings');

            $fieldset->control('input:text', 'name')
                    ->label(trans('antares/logger::labels.device.name'));

            $fieldset->control('button', 'button')
                    ->attributes(['type' => 'submit', 'class' => 'btn btn--md btn--primary mdl-button mdl-js-button'])
                    ->value(trans('antares/logger::labels.save_changes'));

            $fieldset->control('button', 'cancel')
                    ->field(function() {
                        return app('html')->link(handles("antares::logger/devices/index"), trans('antares/logger::labels.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                    });
        });
    }

    /**
     * {@inheritdoc}
     */
    public function handles($url)
    {
        return handles($url);
    }

    /**
     * {@inheritdoc}
     */
    public function setupForm(Grid $form)
    {
        $form->name('Device form');
        $form->hidden('id');
        $form->ajaxable();
        return $form;
    }

}
