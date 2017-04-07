<?php

namespace Antares\Foundation\Listeners;

use Antares\Extension\Events\ComposerFailed;
use Antares\Extension\Processors\Progress;

class StopInstallation {

    /**
     * @var Progress
     */
    protected $progress;

    /**
     * StopInstallation constructor.
     * @param Progress $progress
     */
    public function __construct(Progress $progress) {
        $this->progress = $progress;
    }

    /**
     * @param ComposerFailed $event
     */
    public function handle(ComposerFailed $event) {
        $this->progress->setFailed($event->exception->getMessage());
        $this->progress->save();
    }

}
