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
use Antares\View\Notification\Notification;
use Antares\Contracts\Html\Form\Presenter;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Html\Form\Grid as HtmlGrid;
use Illuminate\Database\Eloquent\Model;
use Antares\Contracts\Html\Form\Grid;
use Antares\Html\Form\ClientScript;
use Antares\Html\Form\FormBuilder;
use Illuminate\View\View;
use RuntimeException;

class Email extends FormBuilder implements Presenter
{

    /**
     * model instance
     *
     * @var Model
     */
    protected $model;

    /**
     * form validation rules
     *
     * @var array
     */
    protected $rules = [
        'header' => ['min:10'],
        'footer' => ['min:3'],
        'styles' => ['min:10']
    ];

    /**
     * default values for email branding
     * 
     * @var array
     */
    protected static $defaults = [];

    /**
     * constructing
     * 
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model  = $model;
        parent::__construct(app(HtmlGrid::class), app(ClientScript::class), app(Container::class));
        $this->grid   = $this->setupForm($this->grid);
        $this->grid->simple(handles('antares::brands/' . $model->id . '/email'), ['method' => 'POST'], $model);
        $notification = app(Notification::class);
        $this->grid->layout('antares/foundation::brands.partials._email', ['variables' => $notification->getVariables(), 'instructions' => $notification->getInstructions()]);
        $this->fieldsets();
        $this->grid->rules($this->rules);
        view()->share('content_class', 'page-email-settings');
    }

    /**
     * creates main email branding fieldsets
     * 
     * @return \Antares\Html\Form\Fieldset
     */
    protected function fieldsets()
    {
        $attributes = ['rows' => 20, 'cols' => 80];
        $this->grid->fieldset('Header', function (Fieldset $fieldset) use($attributes) {
            $control = $fieldset->control('input:hidden', 'area');
            if (!is_null($area    = from_route('area'))) {
                $control->value($area->getId());
            }

            $fieldset->attributes(['panel_id' => 'header-panel']);
            $fieldset->control('textarea', 'header')
                    ->label(trans('antares/brands::label.brand.header'))
                    ->attributes($attributes + ['id' => 'email-header-html', 'panel_id' => 'header-panel'])
                    ->value(is_null($this->model->options) ? $this->getDefault('header') : $this->model->options->header);
        });

        $this->grid->fieldset('Styles', function (Fieldset $fieldset) use($attributes) {
            $fieldset->attributes(['panel_id' => 'styles-panel']);
            $fieldset->control('textarea', 'styles')
                    ->label(trans('antares/brands::label.brand.styles'))
                    ->attributes($attributes + ['id' => 'email-styles'])
                    ->value(is_null($this->model->options) ? $this->getDefault('styles') : $this->model->options->styles);
        });
        $this->grid->fieldset('Footer', function (Fieldset $fieldset) use($attributes) {
            $fieldset->attributes(['panel_id' => 'footer-panel']);
            $fieldset->control('textarea', 'footer')
                    ->label(trans('antares/brands::label.brand.footer'))
                    ->attributes($attributes + ['id' => 'email-footer-html', 'panel_id' => 'footer-panel'])
                    ->value(is_null($this->model->options) ? $this->getDefault('footer') : $this->model->options->footer);

            $fieldset->control('button', 'button')
                    ->attributes(['type' => 'submit', 'class' => 'btn btn--md btn--primary mdl-button mdl-js-button'])
                    ->value(trans('antares/brands::label.brand.save_changes'));
            if (extension_active('multibrand')) {
                $fieldset->control('button', 'cancel')
                        ->field(function() {
                            return app('html')->link(handles("antares::multibrand/index"), trans('antares/brands::label.brand.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                        });
            }
        });
        return $this->grid;
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
        $form->name('Brands Email Form');
        $form->hidden('id');
        $form->ajaxable();
        return $form;
    }

    /**
     * retrives default value for email branding section
     * 
     * @param String $keyname
     * @return String
     * @throws RuntimeException
     */
    protected function getDefault($keyname)
    {
        if (empty(self::$defaults)) {
            self::$defaults = config('antares/brands::default');
        }
        if (!isset(self::$defaults[$keyname])) {
            throw new RuntimeException(sprintf('Unable to find default value for %s', $keyname));
        }
        $view = array_get(self::$defaults, $keyname);
        return ($view instanceof View) ? $view->render() : $view;
    }

}
