<?php

namespace Antares\Events\Views;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;

class AfterRenderTemplate extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Before render template';

    /** @var string */
    protected static $description = 'Runs after template is rendered';

    /** @var string */
    public $template;

    /**
     * AfterRenderTemplate constructor
     *
     * @param string $template
     */
    public function __construct(string $template)
    {
        $this->template = $template;

        parent::__construct();
    }

}
