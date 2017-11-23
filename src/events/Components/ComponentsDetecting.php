<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;

class ComponentsDetecting extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Detecting';

    /** @var string */
    protected static $description = 'Runs before components detecting';

    /**
     * ComponentsDetecting constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
