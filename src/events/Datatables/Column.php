<?php

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
    public function __construct(string $uri, string $columnName, array &$attributes)
    {
        $this->uri = $uri;
        $this->columnName = $columnName;
        $this->attributes = $attributes;

        parent::__construct();
    }

}
