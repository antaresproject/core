<?php

namespace Antares\Foundation\Listeners;

use Antares\Extension\Events\ComposerFailed;
use Antares\Extension\Contracts\ProgressContract;
use Antares\Extension\Processors\Progress as ExtensionProgress;
use Antares\Installation\Progress as InstallationProgress;

class StopInstallation {

    /**
     * @var ProgressContract
     */
    protected $progress;

    /**
     * StopInstallation constructor.
     */
    public function __construct() {
        $this->progress = app()->make('antares.installed')
            ? app()->make(ExtensionProgress::class)
            : app()->make(InstallationProgress::class);
    }

    /**
     * @param ComposerFailed $event
     */
    public function handle(ComposerFailed $event) {
        $this->progress->setFailed($event->exception->getMessage());
    }

}
