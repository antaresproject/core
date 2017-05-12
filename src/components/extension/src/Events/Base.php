<?php

declare(strict_types = 1);

namespace Antares\Extension\Events;

use Antares\Contracts\Events\EventContract;
use Antares\Extension\Contracts\ExtensionContract;

abstract class Base implements EventContract
{

    /**
     * @var ExtensionContract
     */
    public $extension;

    /**
     * Base constructor.
     * @param ExtensionContract $extension
     */
    public function __construct(ExtensionContract $extension)
    {
        $this->extension = $extension;
    }

}
