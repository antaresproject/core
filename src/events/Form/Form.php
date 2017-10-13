<?php

namespace Antares\Events\Form;

use Antares\Foundation\Events\AbstractEvent;

class Form extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Form created';

    /** @var string */
    protected static $description = 'Runs when form is created';

    /** @var string */
    public $formName;

    /**
     * Form constructor
     *
     * @param string $formName
     */
    public function __construct(string $formName)
    {
        $this->formName = $formName;

        parent::__construct();
    }

}
