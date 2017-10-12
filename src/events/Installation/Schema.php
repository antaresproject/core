<?php

namespace Antares\Events\Installation;

use Antares\Foundation\Events\AbstractEvent;

class Schema extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares install: Schema installation started';

    /** @var string */
    protected static $description = 'Runs after schema\'s installation start';

    /**
     * Schema constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
