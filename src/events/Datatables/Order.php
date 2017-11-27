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

class Order extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatables: Order';

    /** @var string */
    protected static $description = 'Runs on Datatable column order';

    /** @var string */
    public $column;

    /** @var */
    public $queryBuilder;

    /** @var string */
    public $direction;

    /**
     * Order constructor
     *
     * @param string $column
     * @param        $queryBuilder
     * @param string $direction
     */
    public function __construct(string $column, $queryBuilder, string $direction)
    {
        $this->column = $column;
        $this->queryBuilder = $queryBuilder;
        $this->direction = $direction;

        parent::__construct();
    }

}
