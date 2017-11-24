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

namespace Antares\Events\Installation;

use Illuminate\Database\Schema\Blueprint;
use Antares\Foundation\Events\AbstractEvent;

class SchemaInstalled extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares install: Table schema installed';

    /** @var string */
    protected static $description = 'Runs after table schema installed';

    /** @var Blueprint */
    public $table;

    /** @var string */
    public $schemaName;

    /**
     * SchemaInstalled constructor
     *
     * @param string    $schemaName
     * @param Blueprint $table
     */
    public function __construct(string $schemaName, Blueprint $table)
    {
        $this->schemaName = $schemaName;
        $this->table = $table;

        parent::__construct();
    }

}
