<?php

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
