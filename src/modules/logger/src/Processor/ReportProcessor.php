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



namespace Antares\Logger\Processor;

use Antares\Logger\Contracts\ReportPresenter as Presenter;
use Antares\Contracts\Html\Form\Grid as FormGrid;
use Antares\Logger\Contracts\ReportListener;
use Antares\Foundation\Processor\Processor;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Antares\Html\Form\Fieldset;
use Exception;

class ReportProcessor extends Processor
{

    /**
     * constructing
     * 
     * @param Presenter $presenter
     */
    public function __construct(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * default index action
     * 
     * @return type
     */
    public function send(ReportListener $listener)
    {
        try {
            $form     = $this->form();
            $autoSend = Input::get('autosend') !== null;
            if (!app('request')->isMethod('post')) {
                return $this->presenter->send($form);
            }
            if (!$form->isValid()) {
                return $this->presenter->send($form);
            }

            if (!$autoSend && !is_null(Input::get('always_send'))) {
                app('antares.memory')->make('primary')->push('notification.send.always', true);
            }
            app('antares.logger')->getAdapter()->setParams(Input::all())->send();
            return $listener->sendSuccess();
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->sendFailed();
        }
    }

    /**
     * create form instance of exception handler
     * 
     * @return Antares\Html\Form\FormBuilder
     */
    protected function form()
    {
        $form = app('antares.form')->of("antares.widgets: report")->extend(function (FormGrid $form) {
            $url = handles('antares::logger/report', ['csrf' => true]);
            $form->name('Report form');
            $form->simple($url, ['id' => 'report-form']);
            $form->layout('antares/logger::admin.report.form');
            $form->fieldset(trans('Error Page Report'), function (Fieldset $fieldset) {
                $location = Input::get('location');
                $fieldset->control('input:hidden', 'url')
                        ->value($location);
                $fieldset->control('textarea', 'description')
                        ->label(trans('Additional message: '))
                        ->attributes(['required' => 'required']);
                $control  = $fieldset->control('input:checkbox', 'always_send')
                        ->label(trans('Always send notification as default action in error page'));
                if ((int) memory('notification.send.always')) {
                    $control->checked();
                }
            });
            $form->ajaxable();
            $form->rules([
                'description' => ['required', 'max:4000', 'min:1']
            ]);
        });
        return $form;
    }

}
