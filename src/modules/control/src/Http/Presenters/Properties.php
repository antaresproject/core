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



namespace Antares\Control\Http\Presenters;

use Illuminate\Contracts\Config\Repository;
use Antares\Control\Contracts\ControlsAdapter;
use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Contracts\Html\Form\Grid as FormGrid;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Fluent;

class Properties extends Presenter
{

    /**
     * Implement of config contract.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * instance of controls adapter
     *
     * @var ControlsAdapter
     */
    protected $adapter;

    /**
     * instance of form factory
     *
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * Create a new resource action presenter.
     * 
     * @param Repository $config
     * @param FormFactory $formFactory
     * @param ControlsAdapter $adapter
     */
    public function __construct(Repository $config, FormFactory $formFactory, ControlsAdapter $adapter)
    {
        $this->formFactory = $formFactory;
        $this->config      = $config;
        $this->adapter     = $adapter;
    }

    /**
     * form presentation
     * 
     * @param numeric $roleId
     * @param numeric $formId
     * @param array $controls
     * @param Eloquent $model
     * @return \Antares\Form\Factory\FormFactory
     */
    public function form($roleId, $formId, array $controls, Eloquent $model)
    {
        $fluent = new Fluent(($model->exists) ? $model->value : []);

        $action = route('control.properties.update', ['roleId' => $roleId, 'formId' => $formId], false);

        $return = $this->formFactory->of('antares.form', function (FormGrid $form) use ($fluent, $action, $controls) {
            $form->name('Properties form');
            $form->setup($this, $action, $fluent, ['class' => 'action-forms form-vertical']);
            $form->hidden('id');
            $this->adapter->adaptee($form, $controls, $fluent);
            $form->layout('antares/control::properties.form');
        });
        return $return;
    }

}
