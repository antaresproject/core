<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Extension\Contracts\ExtensionContract;

class ComponentDeactivating extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component deactivating';

    /** @var string */
    protected static $description = 'Runs before component deactivation';

    /** @var ExtensionContract */
    public $component;

    /**
     * ComponentDeactivating constructor
     *
     * @param ExtensionContract $component
     */
    public function __construct(ExtensionContract $component)
    {
        $this->component = $component;

        parent::__construct();
    }

}
