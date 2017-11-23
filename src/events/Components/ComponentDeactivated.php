<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Extension\Contracts\ExtensionContract;

class ComponentDeactivated extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component deactivated';

    /** @var string */
    protected static $description = 'Runs after component deactivation';

    /** @var ExtensionContract */
    public $component;

    /**
     * ComponentDeactivated constructor
     *
     * @param ExtensionContract $component
     */
    public function __construct(ExtensionContract $component)
    {
        $this->component = $component;

        parent::__construct();
    }

}
