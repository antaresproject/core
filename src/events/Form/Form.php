<?php

namespace Antares\Events\Form;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;
use Illuminate\Database\Eloquent\Model;

class Form extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Form rendered';

    /** @var string */
    protected static $description = 'Runs when form is rendered';

    /** @var string */
    public $formName;

    /** @var FormBuilder */
    public $form;

    /** @var Model|null */
    public $model;

    /** @var string|null */
    public $action;

    /**
     * Form constructor
     *
     * @param string      $formName
     * @param FormBuilder $form
     * @param string|null $action
     * @param Model|null  $model
     */
    public function __construct(string $formName, FormBuilder $form, string $action = null, Model $model = null)
    {
        $this->formName = $formName;
        $this->form = $form;
        $this->action = $action;
        $this->model = $model;

        parent::__construct();
    }

}
