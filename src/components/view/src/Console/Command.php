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


namespace Antares\View\Console;

use Antares\Automation\Model\Jobs;
use Antares\Contracts\Html\Form\Grid as FormGrid;
use Antares\Html\Form\Fieldset;
use Antares\Html\Form\FormBuilder;
use Antares\Support\Fluent;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;

abstract class Command extends BaseCommand
{

    /**
     * whether command can be disabled
     *
     * @var boolean 
     */
    protected $disablable = true;

    /**
     * default cron configuration
     *
     * @var String 
     */
    protected $cron = '*/2 * * * *';

    /**
     * when command can be executed
     *
     * @var array
     */
    protected $availableLaunches = [
        'everyFiveMinutes',
        'everyTenMinutes',
        'everyThirtyMinutes',
        'hourly',
        'daily',
        'dailyAt'    => '13:00',
        'twiceDaily' => [1, 13],
        'weekly',
        'monthly',
        'quarterly',
        'yearly'
    ];

    /**
     * Name of default category automation command
     *
     * @var String
     */
    protected $category = 'system';

    /**
     * command output setter
     * 
     * @param OutputStyle $output
     * @return \Antares\Automation\Console\SyncCommand
     */
    public function setOutput(OutputStyle $output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * Execute the console command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = parent::execute($input, $output);
        $this->finish();
        return $result;
    }

    /**
     * Finish the console command.
     *
     * @return void
     */
    protected function finish()
    {
        $this->laravel['antares.memory']->finish();
    }

    /**
     * checks whether command is standalone
     * 
     * @return boolean
     */
    public function isStandalone()
    {

        if (isset($this->standalone)) {
            return $this->standalone;
        }
        return false;
    }

    /**
     * command title getter
     * 
     * @return boolean|String
     */
    public function getTitle()
    {
        if (isset($this->title)) {
            return $this->title;
        }
        return false;
    }

    /**
     * cron configuration getter
     * 
     * @return boolean|String
     */
    public function getCron()
    {
        if (isset($this->cron)) {
            return $this->cron;
        }
        return false;
    }

    /**
     * command launch time getter
     * 
     * @return boolean|String
     */
    public function getLaunchTime()
    {
        if (isset($this->launched)) {
            return $this->launched;
        }
        return false;
    }

    /**
     * available launch times getter
     * 
     * @return boolean|array
     */
    public function getAvilableLanuchTimes()
    {
        if (isset($this->availableLaunches)) {
            return $this->availableLaunches;
        }
        return false;
    }

    /**
     * command disablable attribute getter
     * 
     * @return boolean
     */
    public function getDisablable()
    {
        if (isset($this->disablable)) {
            return $this->disablable;
        }
        return false;
    }

    /**
     * default command form
     * 
     * @param array $configuration
     * @return FormBuilder
     */
    public function form(array $configuration = array())
    {

        Event::fire('antares.forms', 'commands.' . $this->name);

        return app('antares.form')->of("antares.command: " . $this->name, function (FormGrid $form) use ($configuration) {
                    $form->name('Command Configuration Form');
                    $fluent = new Fluent($configuration);

                    $form->simple(handles('antares::automation/update/'), [], $fluent);
                    $form->hidden('id');

                    $form->fieldset('', function (Fieldset $fieldset) use($configuration) {

                        $launch  = $configuration['launch'];
                        $options = [];
                        foreach ($configuration['launchTimes'] as $option => $value) {
                            if (!is_numeric($option)) {
                                $options[$option] = trans('antares/automation::messages.intervals.' . $option, ['value' => is_array($value) ? implode(', ', $value) : $value]);
                            } else {
                                $options[$value] = trans('antares/automation::messages.intervals.' . $value);
                            }
                        }

                        $fieldset->control('select', 'launch')
                                ->label(trans('Interval'))
                                ->options($options)->value($launch)
                                ->wrapper(['class' => 'w200']);
                        $control = $fieldset->control('checkbox', 'active')
                                ->label(trans('Status'))
                                ->value(1);
                        if ((int) $configuration['active']) {
                            $control->checked();
                        }
                        if (!$this->getDisablable()) {
                            $control->attributes([
                                'disabled' => 'disabled',
                                'readonly' => 'readonly'
                            ]);
                        }
                        $fieldset->control('button', 'button')
                                ->attributes(['type' => 'submit', 'class' => 'btn btn-primary'])
                                ->value(trans('antares/foundation::label.save_changes'));

                        $fieldset->control('button', 'cancel')
                                ->field(function() {
                                    return app('html')->link(handles("antares::automation/index"), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                                });
                    });
                    $form->ajaxable();

                    $form->rules([
                        'title'       => ['required'],
                        'description' => ['required', 'max:4000'],
                    ]);
                });
    }

    /**
     * command configuration getter
     * 
     * @return array
     */
    protected function getConfiguration()
    {
        $model = Jobs::where('name', $this->name)->first();
        return unserialize($model->value);
    }

    /**
     * Command category getter
     * 
     * @return String
     */
    public function getCategory()
    {
        return $this->category;
    }

}
