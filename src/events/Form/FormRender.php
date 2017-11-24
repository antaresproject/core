<?php

namespace Antares\Events\Form;

use Antares\Html\Form\FormBuilder;
use Illuminate\Database\Eloquent\Model;
use Antares\Foundation\Events\AbstractEvent;

class FormRender extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Form: rendered';

    /** @var string */
    protected static $description = 'Runs when form is rendered';

    /** @var string */
    public $formName;

    /** @var FormBuilder */
    public $form;

    /** @var mixed */
    public $model;

    /** @var string|null */
    public $action;

    /**
     * Form constructor
     *
     * @param string      $formName
     * @param FormBuilder $form
     * @param string|null $action
     * @param mixed  $model
     */
    public function __construct(string $formName, FormBuilder $form, string $action = null, $model = null)
    {
        $this->formName = $formName;
        $this->form     = $form;
        $this->action   = $action;
        $this->model    = $model;

        parent::__construct();
    }

}
