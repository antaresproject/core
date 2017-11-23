<?php

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;

class LoadServiceProviders extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Ready: All Service Providers loaded';

    /** @var string */
    protected static $description = 'Runs after all service providers are loaded';

    /**
     * LoadServiceProviders constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
