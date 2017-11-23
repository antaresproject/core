<?php

namespace Antares\Events\Datatables;

use Antares\Support\Collection;
use Antares\Foundation\Events\AbstractEvent;

class BeforeTableAction extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatables: Before table action';

    /** @var string */
    protected static $description = 'Runs before adding table action';

    /** @var string */
    public $uri;

    /** @var string */
    public $actionName;

    /** @var mixed */
    public $row;

    /** @var Collection */
    public $tableActions;

    /**
     * BeforeTableAction constructor
     *
     * @param string     $uri
     * @param string     $actionName
     * @param mixed      $row
     * @param Collection $tableActions
     */
    public function __construct(string $uri, string $actionName, $row, $tableActions)
    {
        $this->uri = $uri;
        $this->actionName = $actionName;
        $this->row = $row;
        $this->tableActions = $tableActions;

        parent::__construct();
    }

}
