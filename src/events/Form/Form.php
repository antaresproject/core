<?php

namespace Antares\Events\Form;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;

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

    /**
     * Form constructor
     *
     * @param string      $formName
     * @param FormBuilder $form
     */
    public function __construct(string $formName, FormBuilder $form)
    {
        $this->formName = $formName;
        $this->form = $form;

        parent::__construct();
    }

}
