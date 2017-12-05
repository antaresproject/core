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

use Antares\Datatables\Html\Builder;
use Antares\Foundation\Events\AbstractEvent;

class AfterColumn extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatables: After column';

    /** @var string */
    protected static $description = 'Runs after column is added';

    /** @var string */
    public $uri;

    /** @var string */
    public $columnName;

    /** @var Builder */
    public $builder;

    /**
     * AfterColumn constructor
     *
     * @param string  $uri
     * @param string  $columnName
     * @param Builder $builder
     */
    public function __construct($uri, $columnName, Builder $builder)
    {
        $this->uri        = $uri;
        $this->columnName = $columnName;
        $this->builder    = $builder;

        parent::__construct();
    }

}
