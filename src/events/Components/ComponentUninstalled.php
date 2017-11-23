<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Extension\Contracts\ExtensionContract;

class ComponentUninstalled extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component uninstalling';

    /** @var string */
    protected static $description = 'Runs beofre component uninstallation';

    /** @var ExtensionContract */
    public $component;

    /**
     * ComponentUninstalled constructor
     *
     * @param ExtensionContract $component
     */
    public function __construct(ExtensionContract $component)
    {
        $this->component = $component;

        parent::__construct();
    }

}
