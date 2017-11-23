<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Extension\Contracts\ExtensionContract;

class ComponentActivating extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component activating';

    /** @var string */
    protected static $description = 'Runs before component activation';

    /** @var ExtensionContract */
    public $component;

    /**
     * ComponentActivating constructor
     *
     * @param ExtensionContract $component
     */
    public function __construct(ExtensionContract $component)
    {
        $this->component = $component;

        parent::__construct();
    }

}
