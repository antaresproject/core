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
use Antares\Tester\Traits\TestableTrait;
use Antares\Html\Form\Grid as HtmlGrid;
use Illuminate\Database\Eloquent\Model;
use Antares\Html\Form\ClientScript;
use Antares\Html\Form\FormBuilder;

class Mail extends FormBuilder implements Presenter
{

    use TestableTrait;

    /**
     * constructing
     * 
     * @param Model $model
     */
    public function __construct($model)
    {
        parent::__construct(app(HtmlGrid::class), app(ClientScript::class), app(Container::class));
        $this->grid->name('General configuration');
        $this->grid->setup($this, 'antares::settings/mail', $model, ['method' => 'PUT']);
        $this->mailer($model);
    }

    /**
     * Form view generator for email configuration.
     *
     * @param  Grid  $form
     * @param  Fluent  $model
     *
     * @return void
     */
    protected function mailer($model)
    {

        return $this->grid->fieldset(trans('antares/foundation::label.settings.mail'), function (Fieldset $fieldset) use ($model) {
                    $fieldset->legend('Mail configuration');

                    $providers = config('antares/foundation::mail.providers');
                    $fieldset->control('select', 'email_driver')
                            ->label(trans('antares/foundation::label.email.driver'))
                            ->options(function() use($providers) {
                                $return = [];
                                foreach ($providers as $name => $provider) {
                                    array_set($return, $name, $provider['title']);
                                }
                                return $return;
                            })
                            ->attributes(['class' => 'mail-change-driver'])
                            ->fieldClass('w200')
                            ->help(trans('antares/foundation::messages.form.help.driver'));
                    $fields  = array_get($providers, 'smtp.fields');
                    $default = 'smtp';
                    foreach ($providers as $name => $provider) {
                        $fields = array_get($provider, 'fields');
                        foreach ($fields as $fieldname => $attributes) {
                            $control = $fieldset->control(array_get($attributes, 'type'), $fieldname)
                                    ->label(trans('antares/foundation::label.' . $fieldname))
                                    ->attributes(['class' => 'mail-control'])
                                    ->block(['role' => $name])
                                    ->help(trans('antares/foundation::messages.form.help.' . $fieldname));
                            if (!is_null($options = array_get($attributes, 'options'))) {
                                $control->options($options);
                            }
                            $control->fieldClass(array_get($attributes, 'fieldClass', 'w350'));


                            if ($name !== $default) {
                                $control->block(['class' => 'hidden-block']);
                            }
                        }
                    }
                    $fieldset->control('button', 'button')
                            ->attributes(['type' => 'submit'])
                            ->value(trans('antares/foundation::label.save_changes'));

                    $this->addTestButton('Mail server connection tester', [
                        'form'      => $this->grid,
                        'title'     => 'Test Connection',
                        'validator' => \Antares\Foundation\Tester\ConnectionTester::class,
                        'data'      => $model->toArray()
                    ]);


                    $fieldset->control('button', 'cancel')
                            ->field(function() {
                                return app('html')->link(handles("antares::/"), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                            });
                });
    }

}
