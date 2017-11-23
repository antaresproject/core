<?php

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;

class AntaresReady extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Ready';

    /** @var string */
    protected static $description = 'Runs after Antares is ready';

    /**
     * AntaresReady constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
