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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Http\Presenters;

use Antares\UI\UIComponents\Contracts\AfterValidate as AfterValidateAdapter;
use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Foundation\Http\Presenters\Presenter;
use Antares\Contracts\Html\Form\Grid as FormGrid;

class UpdatePresenter extends Presenter
{

    /**
     * @var Antares\UI\UIComponents\Contracts\AfterValidate 
     */
    protected $afterValidateAdapter;

    /**
     * Constructing a new module widgets presenter     
     * 
     * @param FormFactory $form
     * @param AfterValidateAdapter $afterValidateAdapter
     */
    public function __construct(FormFactory $form, AfterValidateAdapter $afterValidateAdapter)
    {
        $this->form                 = $form;
        $this->afterValidateAdapter = $afterValidateAdapter;
    }

    /**
     * Form edition 
     * 
     * @param Eloquent $model
     * @param String $url
     * @return \Collective\Html\FormBuilder
     */
    public function form($name, $model, $url)
    {

        $form = $this->form->of("antares.ui-components: custom", function (FormGrid $form) use ($name, $model, $url) {
            $form->name('Ui component form');
            $form->resource($this, $url, $model, ['class' => 'form--hor']);
            $form->hidden('id');
            $form->ajaxable([
                'afterValidate' => $this->afterValidateAdapter->afterValidate(handles('antares/ui-components::ui-components/' . $model->id))
            ]);
            $form->layout('antares/ui-components::admin.partials._form', ['id' => $model->id]);
        });
        $className = app('antares.memory')->make('ui-components')->get($name . '.name');
        app($className)->form();
        return $form;
    }

}
