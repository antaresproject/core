<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;

class ComponentStarted extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component started';

    /** @var string */
    protected static $description = 'Runs after compontent has booted';

    /** @var string */
    public $componentName;

    /**
     * ComponentStarted constructor
     *
     * @param string $componentName
     */
    public function __construct(string $componentName)
    {
        $this->componentName = $componentName;

        parent::__construct();
    }

}
