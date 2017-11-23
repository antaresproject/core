<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Extension\Contracts\ExtensionContract;

class ComponentUninstalling extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component uninstalling';

    /** @var string */
    protected static $description = 'Runs beofre component uninstallation';

    /** @var ExtensionContract */
    public $component;

    /**
     * ComponentUninstalling constructor
     *
     * @param ExtensionContract $component
     */
    public function __construct(ExtensionContract $component)
    {
        $this->component = $component;

        parent::__construct();
    }

}
