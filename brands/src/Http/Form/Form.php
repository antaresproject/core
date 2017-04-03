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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Brands\Http\Form;

use Illuminate\Contracts\Container\Container;
use Antares\Translations\Models\Languages;
use Antares\Contracts\Html\Form\Presenter;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Html\Form\Grid as HtmlGrid;
use Illuminate\Database\Eloquent\Model;
use Antares\Contracts\Html\Form\Grid;
use Antares\Brands\Model\DateFormat;
use Antares\Html\Form\ClientScript;
use Antares\Html\Form\FormBuilder;
use Antares\Brands\Model\Country;

class Form extends FormBuilder implements Presenter
{

    /**
     * model instance
     *
     * @var Model
     */
    protected $model;

    /**
     * constructing
     * 
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        parent::__construct(app(HtmlGrid::class), app(ClientScript::class), app(Container::class));
        $isNew       = $model->id == null;
        $this->grid  = $this->setupForm($this->grid);
        $url         = (extension_active('multibrand') and ! $model->exists) ? 'antares::multibrand' : 'antares::brands';
        $this->grid->resource($this, handles($url), $model);
        $this->grid->rules([
            'name'        => ['required', 'unique:tbl_brands,id' . (($isNew) ? '' : ',' . $model->id)],
            'description' => 'required',
            'url'         => 'regex:/^(?![-.])[a-zA-Z0-9.-]+(?<![-.])$/'
        ]);

        $this->controlsFieldset();
        $this->grid->layout('antares/foundation::brands.partials._form');
    }

    /**
     * creates main controls fieldset
     * 
     * @return \Antares\Html\Form\Fieldset
     */
    protected function controlsFieldset()
    {
        return $this->grid->fieldset('info', function (Fieldset $fieldset) {
                    $fieldset->legend('Brand info');

                    $fieldset->control('input:text', 'name')
                            ->label(trans('antares/brands::label.brand.name'))
                            ->attributes(['placeholder' => trans('antares/brands::label.brand.name')]);

                    $maintenance = $fieldset->control('switch', 'maintenance_mode')
                            ->label(trans('antares/brands::label.brand.maintenance_mode'))
                            ->tooltip(trans('antares/brands::label.brand.maintenance_mode_tooltip'));
                    if (($this->model->exists and $this->model->options->maintenance > 0) or ! $this->model->exists) {
                        $maintenance->checked();
                    }
                    $fieldset->control('input:text', 'url')
                            ->label(trans('antares/brands::label.brand.url'))
                            ->attributes(['placeholder' => trans('antares/brands::label.brand.url')])
                            ->fieldClass('input-field--group input-field--pre')
                            ->before('<div class="input-field__pre"><span>' . (request()->secure() ? 'https://' : 'http://') . '</span></div>')
                            ->value(!is_null($this->model->options) ? $this->model->options->url : '');

                    $dateFormat = $fieldset->control('select', 'date_format')
                            ->wrapper(['class' => 'w220'])
                            ->label(trans('antares/brands::label.brand.date_format'))
                            ->options(function() {
                        return app(DateFormat::class)->query()->get()->pluck('format', 'id');
                    });
                    $dateFormatModel = $this->model->options()->first();

                    if (!is_null($dateFormatModel)) {
                        $dateFormat->value($dateFormatModel->date_format_id);
                    }
                    $options = app(Country::class)->query()->get()->pluck('name', 'code');
                    $country = $fieldset->control('select', 'default_country')
                            ->label(trans('antares/brands::label.brand.default_country'))
                            ->attributes(['data-flag-select', 'data-selectAR' => true, 'class' => 'w200'])
                            ->fieldClass('input-field--icon')
                            ->prepend('<span class = "input-field__icon"><span class = "flag-icon"></span></span>')
                            ->optionsData(function() use($options) {
                                $codes  = $options->keys()->toArray();
                                $return = [];
                                foreach ($codes as $code) {
                                    array_set($return, $code, ['country' => $code]);
                                }
                                return $return;
                            })
                            ->options($options);
                    $optionsModel = $this->model->options()->first();

                    if (!is_null($optionsModel)) {
                        $country->value($optionsModel->country()->first()->code);
                    }
                    $langs = app(Languages::class)->query()->get()->pluck('name', 'code');
                    $fieldset->control('select', 'default_language')
                            ->label(trans('antares/brands::label.brand.default_language'))
                            ->attributes(['data-flag-select', 'data-selectAR' => true, 'class' => 'w300'])
                            ->fieldClass('input-field--icon')
                            ->prepend('<span class = "input-field__icon"><span class = "flag-icon"></span></span>')
                            ->options($langs)
                            ->optionsData(function() use($langs) {
                                $codes  = $langs->keys()->toArray();
                                $return = [];
                                foreach ($codes as $code) {
                                    $flag = $code == 'en' ? 'us' : $code;
                                    array_set($return, $code, ['country' => $flag]);
                                }
                                return $return;
                            })
                            ->value(function() use($optionsModel) {
                                if (!is_null($optionsModel)) {
                                    return $optionsModel->language()->first()->code;
                                }
                            });




                    if (!$this->model->exists) {
                        $fieldset->control('input:checkbox', 'import')
                                ->label(trans('Import brand configuration from existing'))
                                ->value(1)
                                ->attributes([
                                    'class' => 'brand-selector'
                        ]);
                        $brands = $this->container->make('antares.brand')->get()->pluck('name', 'id');
                        $fieldset->control('select', 'brands')
                                ->label(trans('Actual brands'))
                                ->options($brands)
                                ->wrapper(['class' => 'brands-select-container hidden']);
                    }

                    $fieldset->control('button', 'button')
                            ->attributes(['type' => 'submit', 'value' => trans('Submit'), 'class' => 'btn btn--md btn--primary mdl-button mdl-js-button mdl-js-ripple-effect'])
                            ->value(trans('antares/brands::label.brand.save_changes'));

                    if (extension_active('multibrand')) {
                        $fieldset->control('button', 'cancel')
                                ->field(function() {
                                    return app('html')->link(handles("antares::multibrand/index"), trans('antares/multibrand::label.brand.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button mdl-js-ripple-effect']);
                                });
                    }
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
        $form->name('Brands form');
        $form->hidden('id');
        $form->ajaxable();
        return $form;
    }

}
