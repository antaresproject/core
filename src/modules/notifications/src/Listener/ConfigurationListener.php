<?php

namespace Antares\Notifications\Listener;

use Antares\Memory\Model\Option;
use Antares\Html\Form\Fieldset;
use Illuminate\Support\Fluent;
use Antares\Html\Form\Grid;

class ConfigurationListener
{

    /**
     * Handles the security form event.
     *
     * @param SecurityFormSubmitted $securityFormSubmitted
     */
    public function handle(Fluent $model, Grid $grid)
    {
        $grid->fieldset(function(Fieldset $fieldset) {
            $fieldset->legend(trans('antares/notifications::logs.form.notifications_config_legend'));

            $fieldset->control('input:text', 'days')
                    ->label('')
                    ->field(function() {
                        $memory = app('antares.memory')->make('primary');
                        return '<div class="col-group"><div class="col-dt-16 col-16 col-mb-16">' . trans('antares/notifications::logs.form.notifications_config_days_label', ['x' => '<input class="w50" type="number" name="days" value="' . $memory->get('notifications_remove_after_days', '') . '" size="2" max-length="2"  />']) . '</div><div class="col-dt-16 col-16 col-mb-16 "><div class="input-field__desc">' . trans('antares/notifications::logs.form.notifications_config_days_help') . '</div></div></div>';
                    });
        });
        $grid->rules(array_merge($grid->rules, [
            'days' => ['numeric'],
        ]));
    }

    /**
     * Save notifications configuration
     * 
     * @param Option $model
     */
    public function updated(Option $model)
    {

        $model        = Option::query()->firstOrNew([
            'name' => 'notifications_remove_after_days'
        ]);
        $model->value = input('days');
        return $model->save();
    }

}
