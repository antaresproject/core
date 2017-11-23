<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;

class ComponentSaved extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component Booted';

    /** @var string */
    protected static $description = 'Runs after component settings has been saved';

    /** @var string */
    public $componentName;

    /** @var array */
    public $data;

    /**
     * ComponentBooted constructor
     *
     * @param string $componentName
     * @param array  $data
     */
    public function __construct(string $componentName, array $data = [])
    {
        $this->componentName = $componentName;
        $this->data = $data;

        parent::__construct();
    }

}
