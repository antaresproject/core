<?php

namespace Antares\Installation\Listeners;

use Antares\Extension\Events\ComposerFailed;
use Antares\Extension\Events\Failed;
use Illuminate\Events\Dispatcher;
use Antares\Extension\Contracts\ProgressContract;
use Antares\Installation\Progress;

class FailedListener {

    /**
     * @var ProgressContract
     */
    protected $progress;

    /**
     * FailedListener constructor.
     * @param Progress $progress
     */
    public function __construct(Progress $progress) {
        $this->progress = $progress;
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
        if( ! app()->make('antares.installed') ) {
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

}
