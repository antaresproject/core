<?php

namespace Antares\Events\Datatables;

use Antares\Foundation\Events\AbstractEvent;

class AfterMassActionsAction extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatables: After MassActions Action';

    /** @var string */
    protected static $description = 'Runs after Datatable Mass Action is rendered';

    /** @var string */
    public $uri;

    /** @var string */
    public $actionName;

    /** @var mixed */
    public $model;

    /** @var array */
    public $massActions = [];

    /**
     * BeforeMassActionsAction constructor
     *
     * @param string $uri
     * @param string $actionName
     * @param mixed  $model
     * @param array  $massActions
     */
    public function __construct(string $uri, string $actionName, $model, array $massActions = [])
    {
        $this->uri = $uri;
        $this->actionName = $actionName;
        $this->model = $model;
        $this->massActions = $massActions;

        parent::__construct();
    }

}
