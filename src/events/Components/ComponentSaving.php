<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;

class ComponentSaving extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component settings saving';

    /** @var string */
    protected static $description = 'Runs before saving component settings';

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
    public function __construct(string $componentName, array &$data = [])
    {
        $this->componentName = $componentName;
        $this->data = $data;

        parent::__construct();
    }

}
