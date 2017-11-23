<?php

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Memory\Provider;

class AntaresStarted extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Started';

    /** @var string */
    protected static $description = 'Runs after Antares started';

    /** @var Provider */
    public $memory;

    /**
     * AntaresStarted constructor
     *
     * @param Provider $memory
     */
    public function __construct(Provider $memory)
    {
        $this->memory = $memory;

        parent::__construct();
    }

}
