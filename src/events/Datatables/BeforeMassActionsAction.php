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

class BeforeMassActionsAction extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatables: Before MassActions Action';

    /** @var string */
    protected static $description = 'Runs before Datatable Mass Action is rendered';

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
