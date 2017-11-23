<?php

namespace Antares\Events\Datatables;

use Antares\Datatables\Engines\EloquentEngine;
use Antares\Foundation\Events\AbstractEvent;

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
    public function __construct(string $uri, EloquentEngine $datatable)
    {
        $this->uri = $uri;
        $this->datatable = $datatable;

        parent::__construct();
    }

}
