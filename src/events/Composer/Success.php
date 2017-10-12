<?php

namespace Antares\Events\Composer;

use Antares\Foundation\Events\AbstractEvent;

class Success extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Composer: Command succeeded';

    /** @var string */
    protected static $description = 'Runs after composer command succeeded';

    /** @var string */
    public $command;

    /**
     * Success constructor
     *
     * @param string $command
     */
    public function __construct(string $command)
    {
        $this->command = $command;

        parent::__construct();
    }

}
