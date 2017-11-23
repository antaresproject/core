<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;

class ComponentBooted extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component booted';

    /** @var string */
    protected static $description = 'Runs after component has booted';

    /** @var string */
    public $componentName;

    /**
     * ComponentBooted constructor
     *
     * @param string $componentName
     */
    public function __construct(string $componentName)
    {
        $this->componentName = $componentName;

        parent::__construct();
    }

}
