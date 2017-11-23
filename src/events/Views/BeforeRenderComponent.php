<?php

namespace Antares\Events\Views;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;

class BeforeRenderComponent extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Before render component';

    /** @var string */
    protected static $description = 'Runs before component is rendered';

    /** @var string */
    public $template;

    /** @var string */
    public $componentName;

    /**
     * BeforeRenderComponent constructor
     *
     * @param string $template
     * @param string $componentName
     */
    public function __construct(string $template, string $componentName)
    {
        $this->template = $template;
        $this->componentName = $componentName;

        parent::__construct();
    }

}
