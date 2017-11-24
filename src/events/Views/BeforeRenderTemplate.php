<?php

namespace Antares\Events\Views;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;

class BeforeRenderTemplate extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Before render template';

    /** @var string */
    protected static $description = 'Runs before template is rendered';

    /** @var string */
    public $template;

    /**
     * BeforeRenderTemplate constructor
     *
     * @param mixed $template
     */
    public function __construct($template)
    {
        $this->template = $template;

        parent::__construct();
    }

}
