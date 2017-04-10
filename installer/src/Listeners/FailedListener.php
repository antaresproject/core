<?php

namespace Antares\Installation\Listeners;

use Antares\Extension\Events\ComposerFailed;
use Antares\Extension\Events\Failed;
use Illuminate\Events\Dispatcher;
use Antares\Extension\Contracts\ProgressContract;
use Antares\Extension\Processors\Progress as ExtensionProgress;
use Antares\Installation\Progress as InstallationProgress;

class FailedListener {

    /**
     * @var ProgressContract
     */
    protected $progress;

    /**
     * FailedListener constructor.
     */
    public function __construct() {
        $this->progress = app()->make('antares.installed')
            ? app()->make(ExtensionProgress::class)
            : app()->make(InstallationProgress::class);
    }

    /**
     * @param Failed $event
     */
    public function onExtensionFailed(Failed $event) {
        $this->progress->setFailed($event->exception->getMessage());
    }

    /**
     * @param ComposerFailed $event
     */
    public function onComposerFailed(ComposerFailed $event) {
        $this->progress->setFailed($event->exception->getMessage());
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            Failed::class,
            static::class . '@onExtensionFailed'
        );

        $events->listen(
            ComposerFailed::class,
            static::class . '@onComposerFailed'
        );
    }

}
