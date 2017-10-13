<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;

class ComponentTemplatesDetecting extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Detecting templates';

    /** @var string */
    protected static $description = 'Runs before component templates detecting';

    /**
     * ComponentTemplatesDetecting constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}
