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

namespace Antares\Events\Datatables;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Datatables\Engines\BaseEngine;

class Value extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatables: Value';

    /** @var string */
    protected static $description = 'Allows user to change datatable';

    /** @var string */
    public $uri;

    /** @var EloquentEngine */
    public $datatable;

    /**
     * Value constructor
     *
     * @param string         $uri
     * @param EloquentEngine $datatable
     */
    public function __construct(string $uri, BaseEngine $datatable)
    {
        $this->uri       = $uri;
        $this->datatable = $datatable;

        parent::__construct();
    }

}
