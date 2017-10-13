<?php

namespace Antares\Events\Form;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;

class FormReady extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Form ready';

    /** @var string */
    protected static $description = 'Runs when form is ready';

    /** @var string */
    public $formBuilder;

    /**
     * FormReady constructor
     *
     * @param FormBuilder $formBuilder
     */
    public function __construct(FormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;

        parent::__construct();
    }

}
