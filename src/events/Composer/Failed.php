<?php

namespace Antares\Events\Composer;

use Antares\Foundation\Events\AbstractEvent;

class Failed extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Composer: Command failed';

    /** @var string */
    protected static $description = 'Runs after composer command failed';

    /** @var string */
    public $command;

    /** @var \Exception */
    public $exception;

    /**
     * Failed constructor
     *
     * @param string     $command
     * @param \Exception $extension
     */
    public function __construct(string $command, \Exception $extension)
    {
        $this->command = $command;
        $this->exception = $extension;

        parent::__construct();
    }

}
