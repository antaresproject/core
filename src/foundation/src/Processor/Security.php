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


namespace Antares\Foundation\Processor;

use Antares\Foundation\Events\SecurityFormSubmitted;
use Antares\Html\Form\Grid as FormGrid;
use Antares\Html\Form\Fieldset;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Antares\Contracts\Foundation\Events\FormResponseContract;

class Security extends Processor
{

    /**
     * Events dispatcher instance.
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Security constructor.
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Index action
     *
     * @return array
     */
    public function index()
    {
        return ['form' => $this->form()];
    }

    /**
     * Creates form instance
     *
     * @return \Antares\Html\Form\FormBuilder
     */
    protected function form()
    {
        return app('antares.form')->of('security', function (FormGrid $form) {

                    $form->name('Security Form');

                    $form->fieldset(function (Fieldset $fieldset) {

                        $fieldset->control('button', 'button')
                                ->attributes(['type' => 'submit', 'class' => 'btn btn-primary',])
                                ->value(trans('antares/foundation::label.save_changes'));
                    });
                });
    }

    /**
     * Fires an event of the security form with the given request instance.
     *
     * @param FormResponseContract $listener
     * @param Request $request
     * @return mixed
     */
    public function submit(FormResponseContract $listener, Request $request)
    {
        $response = (array) $this->dispatcher->fire(new SecurityFormSubmitted($listener, $request));
        if (count($response)) {
            return $response[0];
        }

        return null;
    }

}
