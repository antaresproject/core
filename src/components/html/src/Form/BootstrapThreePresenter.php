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

namespace Antares\Html\Form;

use Antares\Contracts\Html\Form\Field as FieldContract;
use Antares\Html\FormBuilder as BaseFormBuilder;
use Antares\Html\HtmlBuilder as BaseHtmlBuilder;
use Antares\Asset\Factory as AssetFactory;
use Antares\Contracts\Html\Form\Template;
use Illuminate\Support\Traits\Macroable;
use Antares\Html\Control\RemoteSelect;
use Antares\Html\Control\Dropzone;

class BootstrapThreePresenter implements Template
{

    use Macroable;

    /**
     * Form builder.
     *
     * @var \Antares\Html\FormBuilder
     */
    protected $form;

    /**
     * Html builder.
     *
     * @var \Antares\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Asset factory.
     *
     * @var \Antares\Asset\Factory
     */
    protected $asset;

    /**
     * Construct a new presenter.
     *
     * @param \Antares\Html\FormBuilder  $form
     * @param \Antares\Html\HtmlBuilder  $html
     */
    public function __construct(BaseFormBuilder $form, BaseHtmlBuilder $html, AssetFactory $assetFactory)
    {
        $this->form  = $form;
        $this->html  = $html;
        $this->asset = $assetFactory;
    }

    /**
     * Button template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function button(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'btn btn--md btn--primary mdl-button mdl-js-button mdl-js-ripple-effect', 'type' => $field->type]);
        return $this->form->button($field->get('value'), $attributes);
    }

    /**
     * Checkbox template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function checkbox(FieldContract $field)
    {
        return $this->form->checkbox($field->get('name'), $field->get('value'), $field->get('checked'), $field->get('attributes'));
    }

    /**
     * Checkboxes template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function checkboxes(FieldContract $field)
    {
        return $this->form->checkboxes(
                        $field->get('name'), $field->get('options'), $field->get('checked'), $field->get('attributes')
        );
    }

    /**
     * File template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function file(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'form-control']);
        return $this->form->file($field->get('name'), $attributes);
    }

    /**
     * Input template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function input(FieldContract $field)
    {
        $attributes = $field->get('attributes');
        if (isset($attributes['field'])) {
            unset($attributes['field']);
        }


        $decoratedAttributes = $this->html->decorate($attributes);


        return $this->form->input($field->get('type'), $field->get('name'), $field->get('value'), $decoratedAttributes);
    }

    /**
     * create dropzone field type
     * 
     * @param FieldContract $field
     * @return type
     */
    public function dropzone(FieldContract $field)
    {
        $dropzone = new Dropzone($field, $this->asset, $this->html, $this->form);
        return $dropzone->render();
    }

    /**
     * Password template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function password(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'form-control']);
        return $this->form->password($field->get('name'), $attributes);
    }

    /**
     * Radio template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function radio(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->get('attributes'));
        return $this->form->radio($field->get('name'), $field->get('value'), $field->get('checked'), $attributes);
    }

    /**
     * Select template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function select(FieldContract $field)
    {
        $attributes  = $this->html->decorate($field->get('attributes'));
        $optionsData = $field->get('optionsData') ?: [];
        if (array_get($attributes, 'data-selectar') === false or ! is_null(array_get($attributes, 'data-selectar--search'))) {
            unset($attributes['data-selectar']);
        }
        return $this->form->select(
                        $field->get('name'), $field->get('options'), $field->get('value'), $attributes, $optionsData
        );
    }

    /**
     * Textarea template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function textarea(FieldContract $field)
    {
        $attributes = $this->html->decorate($field->get('attributes'), ['class' => 'form-control']);
        return $this->form->textarea($field->get('name'), $field->get('value'), $attributes);
    }

    /**
     * create dropzone field type
     * 
     * @param FieldContract $field
     * @return String
     */
    public function ckeditor(FieldContract $field)
    {
        $scripts = true;
        if (isset($field->attributes['scripts'])) {
            $scripts = $field->attributes['scripts'];
        }
        if ($scripts) {
            $id        = isset($field->attributes['id']) ? $field->attributes['id'] : $field->get('name');
            $container = $this->asset->container('antares/foundation::scripts');
            app('antares.asset')->container('antares/foundation::application')->add('ckeditor-js', '/packages/ckeditor/ckeditor.js', ['webpack_forms_basic']);
            $init      = <<<EOD
            CKEDITOR.replace('$id',{
                width:'100%'
            });
EOD;
            $container->inlineScript('ckeditor', $init);
        }

        return $this->textarea($field);
    }

    /**
     * creates switch field type
     * 
     * @param FieldContract $field
     * @return Control
     */
    public function switch_field(FieldContract $field)
    {
        return $this->form->checkbox($field->get('name'), $field->get('value'), $field->get('checked'), $field->get('attributes'));
    }

    /**
     * creates remote select field type
     * 
     * @param FieldContract $field
     * @return \Illuminate\View\View
     */
    public function remote_select(FieldContract $field)
    {
        return app(RemoteSelect::class)->setField($field)->render();
    }

}
