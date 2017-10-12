<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Extension\Contracts\ExtensionContract;

class ComponentActivated extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component activated';

    /** @var string */
    protected static $description = 'Runs after component activation';

    /** @var ExtensionContract */
    public $component;

    /**
     * ComponentActivated constructor
     *
     * @param ExtensionContract $component
     */
    public function __construct(ExtensionContract $component)
    {
        $this->component = $component;

        parent::__construct();
    }

}
