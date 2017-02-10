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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Http\Presenters;

use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Contracts\Html\Form\Grid as FormGrid;
use Antares\Foundation\Http\Datatables\Extensions;
use Antares\Foundation\Http\Breadcrumb\Breadcrumb;
use Antares\Contracts\Html\Form\Fieldset;

class Extension extends Presenter
{

    /**
     * Implementation of extension contract.
     *
     * @var \Antares\Contracts\Extension\Factory
     */
    protected $extension;

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * extensions datatable
     *
     * @var Extensions 
     */
    protected $extensions = null;

    /**
     * Construct a new Extension presenter.
     * 
     * @param FormFactory $form
     * @param Breadcrumb $breadcrumb
     * @param Extensions $extensions
     */
    public function __construct(FormFactory $form, Breadcrumb $breadcrumb, Extensions $extensions)
    {
        $this->form       = $form;
        $this->breadcrumb = $breadcrumb;
        $this->extensions = $extensions;
    }

    /**
     * Form View Generator for Antares\Extension.
     *
     * @param  \Illuminate\Support\Fluent  $model
     * @param  string  $name
     *
     * @return \Antares\Contracts\Html\Form\Builder
     */
    public function configure($model, $name)
    {
        $this->breadcrumb->onComponentConfigure($name);
        return $this->form->of("antares.extension: {$name}", function (FormGrid $form) use ($model, $name) {
                    $form->setup($this, "antares::extensions/{$name}/configure", $model);

                    $handles      = data_get($model, 'handles', $this->extension->option($name, 'handles'));
                    $configurable = data_get($model, 'configurable', true);

                    $form->fieldset(function (Fieldset $fieldset) use ($handles, $name, $configurable) {
                        if (!is_null($handles) && $configurable !== false) {
                            $fieldset->control('input:text', 'handles')
                                    ->label(trans('antares/foundation::label.extensions.handles'))
                                    ->value($handles);
                        }

                        $fieldset->control('input:text', 'migrate')
                                ->label(trans('antares/foundation::label.extensions.update'))
                                ->field(function () use ($name) {
                                    return app('html')->link(
                                                    handles("antares::extensions/{$name}/update", ['csrf' => true]), trans('antares/foundation::label.extensions.actions.update'), ['class' => 'btn btn--md btn--primary mdl-button mdl-js-button mdl-js-ripple-effect']
                                    );
                                });
                    });
                });
    }

    /**
     * create table instance
     * 
     * @return \Illuminate\View\View
     */
    public function table()
    {
        $this->breadcrumb->onComponentsList();
        return $this->extensions->render('antares/foundation::extensions.index');
    }

}
