<?php

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;

class ClientReady extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Ready: Client';

    /** @var string */
    protected static $description = 'Runs after Antares is ready';

    /**
     * ClientReady constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
