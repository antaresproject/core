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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Brands\Http\Form;

use Illuminate\Contracts\Container\Container;
use Antares\Contracts\Html\Form\Presenter;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Html\Form\Grid as HtmlGrid;
use Illuminate\Database\Eloquent\Model;
use Antares\Brands\Facade\StylerFacade;
use Antares\Contracts\Html\Form\Grid;
use Antares\Html\Form\ClientScript;
use Antares\Html\Form\FormBuilder;

class Area extends FormBuilder implements Presenter
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
        $this->grid  = $this->setupForm($this->grid);
        $this->grid->simple(handles('antares::brands/' . $model->brand_id . '/area/' . $model->id), [], $this->model);

        $this->controlsFieldset();
        $this->logoFieldset();
        $this->colorsFieldset();
        $this->textColorsFieldsets();
        $this->grid->layout('antares/foundation::brands.partials._area_form');
    }

    /**
     * creates main controls fieldset
     * 
     * @return \Antares\Html\Form\Fieldset
     */
    protected function controlsFieldset()
    {
        return $this->grid->fieldset('info', function (Fieldset $fieldset) {
                    $fieldset->legend(trans('antares/brands::messages.legend.brand_template'));
                    $fieldset->control('radio_btns', 'composition')
                            ->label(trans('antares/brands::label.brand.composition'))
                            ->options($this->getCompositionOptions())
                            ->value($this->model->composition);

                    $fieldset->control('button', 'button')
                            ->attributes(['type' => 'submit', 'value' => trans('Submit'), 'class' => 'btn btn--md btn--primary mdl-button mdl-js-button mdl-js-ripple-effect'])
                            ->value(trans('antares/brands::label.brand.save_changes'));
                    $fieldset->control('button', 'cancel')
                            ->field(function() {
                                return app('html')->link(handles("antares::brands/index"), trans('antares/brands::label.brand.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button mdl-js-ripple-effect']);
                            });
                });
    }

    /**
     * Gets composition options
     * 
     * @return array
     */
    protected function getCompositionOptions()
    {
        $options    = [];
        $compotions = config('antares/brands::compositions');
        foreach ($compotions as $composition) {
            array_set($options, $composition, trans('antares/brands::messages.' . $composition));
        }
        return $options;
    }

    /**
     * creates logo fieldset
     * 
     * @return \Antares\Html\Form\Fieldset
     */
    protected function logoFieldset()
    {
        $rules = config('antares/brands::logo.rules');
        $map   = implode(',', array_map(function($element) {
                    return '.' . $element;
                }, array_get($rules, 'acceptedFiles')));

        $default = array_except($rules, ['acceptedFiles', 'dimensions']) + ['acceptedFiles' => $map];
        return $this->grid->fieldset('logo', function (Fieldset $fieldset) use($default) {

                    $fieldset->legend(trans('antares/brands::messages.legend.brand_logo'));

                    $fieldset->control('dropzone', 'logo')->attributes($default + [
                                "container"       => "DropzoneLogo",
                                "paramName"       => "logo",
                                'thumbnailWidth'  => 190,
                                'thumbnailHeight' => 41,
                                "url"             => handles("antares::brands/upload"),
                                'view'            => 'antares/foundation::brands.partials._dropzone_logo'])
                            ->value($this->model->logo);


                    $fieldset->control('dropzone', 'favicon')->attributes($default + [
                                "container"       => "DropzoneFavicon",
                                "paramName"       => "favicon",
                                'thumbnailWidth'  => 60,
                                'thumbnailHeight' => 60,
                                'onSuccess'       => $this->onSuccessUploadFavicon(),
                                "url"             => handles("antares::brands/upload"),
                                'view'            => 'antares/foundation::brands.partials._dropzone_favicon'])
                            ->value($this->model->favicon);

                    $fieldset->layout('antares/foundation::brands.partials._fieldset_logo');
                });
    }

    /**
     * when custom upload button available
     * 
     * @return type
     */
    protected function onSuccessUploadFavicon()
    {
        return <<<EOD
            $('.main-sidebar .main-sidebar__logo').css('background-image','url(/img/logos/'+response.path+')');
EOD;
    }

    /**
     * creates colors fieldset
     * 
     * @return \Antares\Html\Form\Fieldset
     */
    protected function colorsFieldset()
    {

        $colors       = $this->model->colors;
        StylerFacade::formAdapter($colors)->share();
        $this->colors = app('antares.memory')->make('registry')->get('brand.configuration.options.colors');
        $this->grid->fieldset('colors', function (Fieldset $fieldset) use($colors) {
            $fieldset->legend(trans('antares/brands::messages.legend.brand_colors'));
            $fieldset->attributes([
                'name' => 'color-inputs'
            ]);
            $fieldset->control('input:text', 'colors[main][value]')
                    ->label('Main')
                    ->container('cp-brand--primary')
                    ->attributes(['class' => 'cp', 'maxlength' => 7])
                    ->value(array_get($colors, 'main.value', '02a8f3'));

            $fieldset->control('input:hidden', 'colors[main][mod1]')
                    ->attributes(['class' => 'colors-main-mod1'])
                    ->value(array_get($colors, 'main.mod1'));

            $fieldset->control('input:hidden', 'colors[main][mod2]')
                    ->attributes(['class' => 'colors-main-mod2'])
                    ->value(array_get($colors, 'main.mod2'));

            $fieldset->control('input:hidden', 'colors[main][mod3]')
                    ->attributes(['class' => 'colors-main-mod3'])
                    ->value(array_get($colors, 'main.mod3'));

            $fieldset->layout('antares/foundation::brands.partials._fieldset_colors');
        });

        $this->grid->fieldset('colors', function (Fieldset $fieldset) use($colors) {
            $fieldset->legend(trans('antares/brands::messages.legend.brand_colors'));
            $fieldset->attributes([
                'name' => 'color-inputs'
            ]);

            $fieldset->control('input:text', 'colors[secondary][value]')
                    ->label('Secondary')
                    ->container('cp-brand--secondary')
                    ->attributes(['class' => 'cp', 'maxlength' => 7])
                    ->value(array_get($colors, 'secondary.value', '30343d'));

            $fieldset->control('input:hidden', 'colors[secondary][mod1]')
                    ->attributes(['class' => 'colors-secondary-mod1'])
                    ->value(array_get($colors, 'secondary.mod1'));

            $fieldset->control('input:hidden', 'colors[secondary][mod2]')
                    ->attributes(['class' => 'colors-secondary-mod2'])
                    ->value(array_get($colors, 'secondary.mod2'));

            $fieldset->control('input:hidden', 'colors[secondary][mod3]')
                    ->attributes(['class' => 'colors-secondary-mod3'])
                    ->value(array_get($colors, 'secondary.mod3'));

            $fieldset->layout('antares/foundation::brands.partials._fieldset_colors');
        });
        $this->grid->fieldset('colors', function (Fieldset $fieldset) use($colors) {
            $fieldset->legend(trans('antares/brands::messages.legend.brand_colors'));
            $fieldset->attributes([
                'name' => 'color-inputs'
            ]);
            $fieldset->control('input:text', 'colors[background][value]')
                    ->label('Background')
                    ->container('cp-brand--tetriary')
                    ->attributes(['class' => 'cp', 'maxlength' => 7])
                    ->value(array_get($colors, 'background.value', '70c24a'));

            $fieldset->control('input:hidden', 'colors[background][mod1]')
                    ->attributes(['class' => 'colors-background-mod1'])
                    ->value(array_get($colors, 'background.mod1'));

            $fieldset->control('input:hidden', 'colors[background][mod2]')
                    ->attributes(['class' => 'colors-background-mod2'])
                    ->value(array_get($colors, 'background.mod2'));

            $fieldset->control('input:hidden', 'colors[background][mod3]')
                    ->attributes(['class' => 'colors-background-mod3'])
                    ->value(array_get($colors, 'background.mod3'));

            $fieldset->layout('antares/foundation::brands.partials._fieldset_colors');
        });
        return;
    }

    /**
     * create text colors fieldsets
     * 
     * @return null
     */
    public function textColorsFieldsets()
    {
        $colors = array_get($this->model->colors, 'text', []);
        $this->grid->fieldset('text-color-main', function (Fieldset $fieldset) use($colors) {
            $fieldset->attributes(['classname' => 'cp-brand--primary']);

            $fieldset->control('input:text', 'colors[text][main][first]')
                    ->attributes(['class' => 'cp', 'maxlength' => 7, 'num' => 1])
                    ->value(array_get($colors, 'main.first', '#FFFFFF'));

            $fieldset->control('input:text', 'colors[text][main][second]')
                    ->attributes(['class' => 'cp', 'maxlength' => 7, 'num' => 2])
                    ->value(array_get($colors, 'main.second', '#FFFFFF'));

            $fieldset->layout('antares/foundation::brands.partials._fieldset_text_colors');
        });
        $this->grid->fieldset('text-color-secondary', function (Fieldset $fieldset) use($colors) {
            $fieldset->attributes(['classname' => 'cp-brand--secondary']);

            $fieldset->control('input:text', 'colors[text][secondary][first]')
                    ->attributes(['class' => 'cp', 'maxlength' => 7, 'num' => 3])
                    ->value(array_get($colors, 'secondary.first', '#8a8f99'));

            $fieldset->control('input:text', 'colors[text][secondary][second]')
                    ->attributes(['class' => 'cp', 'maxlength' => 7, 'num' => 4])
                    ->value(array_get($colors, 'secondary.second', '#FFFFFF'));

            $fieldset->layout('antares/foundation::brands.partials._fieldset_text_colors');
        });
        $this->grid->fieldset('text-color-background', function (Fieldset $fieldset) use($colors) {
            $fieldset->attributes(['classname' => 'cp-brand--tetriary']);

            $fieldset->control('input:text', 'colors[text][background][first]')
                    ->attributes(['class' => 'cp', 'maxlength' => 7, 'num' => 5])
                    ->value(array_get($colors, 'background.first', '#000000'));

            $fieldset->control('input:text', 'colors[text][background][second]')
                    ->attributes(['class' => 'cp', 'maxlength' => 7, 'num' => 6])
                    ->value(array_get($colors, 'background.second', '#000000'));

            $fieldset->layout('antares/foundation::brands.partials._fieldset_text_colors');
        });
        return;
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
        return $form;
    }

}
