<?php

namespace Antares\Events\Authentication;

use Antares\Foundation\Events\AbstractEvent;

class AuthCheck extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Authentication: Auth check';

    /** @var string */
    protected static $description = 'Runs when authentication is checked';

    /**
     * AuthCheck constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
