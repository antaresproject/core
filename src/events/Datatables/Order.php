<?php

namespace Antares\Events\Datatables;

use Antares\Foundation\Events\AbstractEvent;

class Order extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatable: Order';

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
