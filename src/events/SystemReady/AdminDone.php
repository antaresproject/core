<?php

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;

class AdminDone extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Done: Admin';

    /** @var string */
    protected static $description = 'Runs after Antares is done';

    /**
     * AntaresDone constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
