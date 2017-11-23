<?php

namespace Antares\Events\Views;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;

class WidgetActions extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Widget Actions';

    /** @var string */
    protected static $description = 'Runs after widget actions are rendered';

    /** @var string */
    public $template;

    /** @var string */
    public $componentName;

    /**
     * AfterRenderComponent constructor
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
