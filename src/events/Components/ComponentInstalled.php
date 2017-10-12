<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Extension\Contracts\ExtensionContract;

class ComponentInstalled extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component installed';

    /** @var string */
    protected static $description = 'Runs after component intallation';

    /** @var ExtensionContract */
    public $component;

    /**
     * ComponentInstalled constructor
     *
     * @param ExtensionContract $component
     */
    public function __construct(ExtensionContract $component)
    {
        $this->component = $component;

        parent::__construct();
    }

}
