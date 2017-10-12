<?php

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;

class ClientStarted extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Stated: Client';

    /** @var string */
    protected static $description = 'Runs after Antares started';

    /**
     * ClientStarted constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
