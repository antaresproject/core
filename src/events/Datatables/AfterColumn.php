<?php

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
    public function __construct(string $uri, string $columnName, Builder $builder)
    {
        $this->uri = $uri;
        $this->columnName = $columnName;
        $this->builder = $builder;

        parent::__construct();
    }

}
