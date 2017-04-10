<?php

namespace Antares\Foundation\Listeners;

use Antares\Extension\Events\Base;
use Antares\Extension\Contracts\ProgressContract;
use Antares\Extension\Processors\Progress as ExtensionProgress;
use Antares\Installation\Progress as InstallationProgress;

class AfterExtensionOperation {

    /**
     * @var ProgressContract
     */
    protected $progress;

    /**
     * AfterExtensionOperation constructor.
     */
    public function __construct() {
        $this->progress = app()->make('antares.installed')
            ? app()->make(ExtensionProgress::class)
            : app()->make(InstallationProgress::class);
    }

    /**
     * @param Base $event
     */
    public function handle(Base $event) {
        $this->progress->setFinished();
    }

}
