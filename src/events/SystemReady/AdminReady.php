<?php

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;

class AdminReady extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Ready: Admin';

    /** @var string */
    protected static $description = 'Runs after Antares is ready';

    /**
     * AdminReady constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
