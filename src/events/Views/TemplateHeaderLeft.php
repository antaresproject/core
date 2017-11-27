<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Events\Views;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;

class TemplateHeaderLeft extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Template header left';

    /** @var string */
    protected static $description = 'Runs after left part of header is rendered';

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
