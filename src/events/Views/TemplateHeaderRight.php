<?php

namespace Antares\Events\Views;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;

class TemplateHeaderRight extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Template header right';

    /** @var string */
    protected static $description = 'Runs after right part of header is rendered';

    /** @var string */
    public $template;

    /** @var string */
    public $widgetName;

    /**
     * AfterRenderTemplate constructor
     *
     * @param string $template
     * @param string $widgetName
     */
    public function __construct(string $template, string $widgetName)
    {
        $this->template = $template;
        $this->widgetName = $widgetName;

        parent::__construct();
    }

}
