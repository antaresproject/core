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

class BreadcrumbBeforeRender extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Before render Breadcrumb';

    /** @var string */
    protected static $description = 'Runs before bredcrumb is rendered';

    /** @var mixed */
    public $items;

    /** @var string */
    public $key;

    /**
     * BreadcrumbBeforeRender constructor
     *
     * @param string $key
     * @param mixed  $items
     */
    public function __construct(string $key, $items)
    {
        $this->key = $key;
        $this->items = $items;

        parent::__construct();
    }

}
