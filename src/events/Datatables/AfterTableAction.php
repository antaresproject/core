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
