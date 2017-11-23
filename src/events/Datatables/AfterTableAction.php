<?php

namespace Antares\Events\Datatables;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Support\Collection;

class AfterTableAction extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatables: After table action';

    /** @var string */
    protected static $description = 'Runs after adding table action';

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
