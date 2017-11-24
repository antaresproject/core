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
     * @param mixed $template
     */
    public function __construct($template)
    {
        $this->template = $template;

        parent::__construct();
    }

}
