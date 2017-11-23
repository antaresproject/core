<?php

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;

class VersionCheck extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Version Check';

    /** @var string */
    protected static $description = 'Runs on dashboard';

    /**
     * VersionCheck constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
