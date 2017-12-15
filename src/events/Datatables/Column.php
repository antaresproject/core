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

class Column extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatables: Column';

    /** @var string */
    protected static $description = 'Allows user to change column attributes (e.g. title)';

    /** @var string */
    public $uri;

    /** @var string */
    public $columnName;

    /** @var array */
    public $attributes = [];

    /**
     * Column constructor
     *
     * @param string $uri
     * @param string $columnName
     * @param array  $attributes
     */
    public function __construct($uri, $columnName, array &$attributes)
    {
        $this->uri        = $uri;
        $this->columnName = $columnName;
        $this->attributes = $attributes;

        parent::__construct();
    }

}
