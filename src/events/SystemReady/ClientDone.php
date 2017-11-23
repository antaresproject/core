<?php

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;

class ClientDone extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Done: Client';

    /** @var string */
    protected static $description = 'Runs after Antares is done';

    /**
     * ClientDone constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
