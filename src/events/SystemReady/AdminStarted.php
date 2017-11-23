<?php

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;

class AdminStarted extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Stated: Admin';

    /** @var string */
    protected static $description = 'Runs after Antares started';

    /**
     * AdminStarted constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
