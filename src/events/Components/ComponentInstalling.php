<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Extension\Contracts\ExtensionContract;

class ComponentInstalling extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component installing';

    /** @var string */
    protected static $description = 'Runs before component intallation';

    /** @var ExtensionContract */
    public $component;

    /**
     * ComponentInstalling constructor
     *
     * @param ExtensionContract $component
     */
    public function __construct(ExtensionContract $component)
    {
        $this->component = $component;

        parent::__construct();
    }

}
