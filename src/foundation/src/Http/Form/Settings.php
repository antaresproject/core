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

class Settings extends FormBuilder implements Presenter
{

    /**
     * constructing
     * 
     * @param Model $model
     */
    public function __construct($model)
    {
        view()->share('grid_container_class', 'grid-container--footer');
        parent::__construct(app(HtmlGrid::class), app(ClientScript::class), app(Container::class));
        Event::fire('antares.forms', 'foundation.settings');
        $this->grid->name('General configuration');

        $this->grid->setup($this, 'antares::settings/index', $model);
        $this->application();
        $this->grid->rules([
            'site_name' => ['required']
        ]);
        Event::fire("antares.form: foundation.settings", [$model, $this->grid, "foundation.settings"]);
    }

    /**
     * Form view generator for application configuration.
     *
     * @param  Grid  $form
     *
     * @return void
     */
    protected function application()
    {
        return $this->grid->fieldset(trans('antares/foundation::label.settings.application'), function (Fieldset $fieldset) {
                    $fieldset->legend('Application configuration');

                    $fieldset->control('select', 'mode')
                            ->label(trans('antares/foundation::label.mode'))
                            ->options([
                                'production'  => trans('antares/foundation::global.modes.production'),
                                'development' => trans('antares/foundation::global.modes.development'),
                            ])->wrapper(['class' => 'w220']);

                    $fieldset->control('button', 'cancel')
                            ->field(function() {
                                return app('html')->link(handles("antares::/"), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                            });

                    $fieldset->control('button', 'button')
                            ->attributes(['type' => 'submit'])
                            ->value(trans('antares/foundation::label.save_changes'));
                });
    }

}
