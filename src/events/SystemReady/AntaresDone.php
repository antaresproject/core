<?php

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;

class AntaresDone extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Done';

    /** @var string */
    protected static $description = 'Runs after Antares is ready';

    /**
     * AntaresDone constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
