<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;

class ComponentsBooted extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Components booted';

    /** @var string */
    protected static $description = 'Runs after all components are booted';

    /**
     * ComponentsBooted constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
