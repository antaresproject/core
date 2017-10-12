<?php

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Extension\Contracts\ExtensionContract;

class ComponentInstallationFailed extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component installation failed';

    /** @var string */
    protected static $description = 'Runs after component intallation failed';

    /** @var ExtensionContract */
    public $component;

    /** @var \Exception */
    public $exception;

    /**
     * ComponentInstallationFailed constructor
     *
     * @param ExtensionContract $component
     * @param \Exception        $exception
     */
    public function __construct(ExtensionContract $component, \Exception $exception)
    {
        $this->component = $component;
        $this->exception = $exception;

        parent::__construct();
    }

}
