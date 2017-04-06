<?php

namespace Antares\Foundation\Listeners;

use Antares\Extension\Events\Base;
use Antares\Extension\Processors\Progress;

class AfterExtensionOperation {

    /**
     * @var Progress
     */
    protected $progress;

    /**
     * AfterExtensionOperation constructor.
     * @param Progress $progress
     */
    public function __construct(Progress $progress) {
        $this->progress = $progress;
    }

    /**
     * @param Base $event
     */
    public function handle(Base $event) {
        $this->progress->setFinished();
        $this->progress->save();
    }

}
