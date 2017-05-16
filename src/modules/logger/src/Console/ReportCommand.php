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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Console;

use Antares\Contracts\Html\Form\Grid as FormGrid;
use Illuminate\Support\Facades\Log;
use Antares\Html\Form\FormBuilder;
use Antares\View\Console\Command;
use Antares\Html\Form\Fieldset;
use Antares\Support\Fluent;
use Exception;

class ReportCommand extends Command
{

    /**
     * human readable command name
     *
     * @var String
     */
    protected $title = 'Analyzer Report';

    /**
     * when command should be executed
     *
     * @var String
     */
    protected $launched = 'daily';

    /**
     * when command can be executed
     *
     * @var array
     */
    protected $availableLaunches = [
        'everyMinute',
        'everyFiveMinutes',
        'everyTenMinutes',
        'everyThirtyMinutes',
        'hourly',
        'daily'
    ];

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'report:analyzer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create analyzer report.';

    /**
     * whether command can be disabled
     *
     * @var boolean 
     */
    protected $disablable = false;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {

        $actions   = array_keys(app('config')->get('antares/logger::analyzer.actions'));
        $contents  = [];
        $processor = app('Antares\Logger\Processor\AnalyzeProcessor');

        foreach ($actions as $action) {
            try {
                array_push($contents, $processor->{$action}()->render());
            } catch (Exception $ex) {
                Log::emergency($ex);
                continue;
            }
        }


        $html     = implode('', $contents);
        $rendered = view('antares/logger::admin.generator.preview', ['html' => $html])->render();
        $filename = str_random() . '.html';
        $path     = storage_path("temp/{$filename}");
        file_put_contents($path, $rendered);
        $this->line($path);
    }

    /**
     * default command form
     * 
     * @param array $configuration
     * @return FormBuilder
     */
    public function form(array $configuration = array())
    {

        return app('antares.form')->of("antares.command: " . $this->name, function (FormGrid $form) use ($configuration) {
                    $form->name('Command Configuration Form');
                    $fluent = new Fluent($configuration);

                    $form->simple(handles('antares::automation/update/'), [], $fluent);
                    $form->hidden('id');

                    $form->fieldset('Fields', function (Fieldset $fieldset) use($configuration) {

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
                                ->options($options)
                                ->value($launch)
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
                                    return app('html')->link(handles("antares::automation/"), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                                });
                    });
                    $form->ajaxable();

                    $form->rules([
                        'title'       => ['required'],
                        'description' => ['required', 'max:4000'],
                    ]);
                });
    }

}
