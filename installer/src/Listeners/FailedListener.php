<?php

namespace Antares\Installation\Listeners;

use Antares\Extension\Events\ComposerFailed;
use Antares\Extension\Events\Failed;
use Illuminate\Events\Dispatcher;
use Antares\Installation\Progress;
use Antares\Memory\Provider;
use Illuminate\Contracts\Container\Container;

class FailedListener {

    /**
     * @var Provider
     */
    protected $memory;

    /**
     * @var Progress
     */
    protected $progress;

    /**
     * IncrementProgress constructor.
     * @param Container $container
     * @param Progress $progress
     */
    public function __construct(Container $container, Progress $progress) {
        $this->memory   = $container->make('antares.memory')->make('primary');
        $this->progress = $progress;
    }

    /**
     * @param Failed $event
     */
    public function onExtensionFailed(Failed $event) {
        $this->progress->setFailed($event->exception->getMessage());
        $this->progress->save();
        $this->memory->finish();
    }

    /**
     * @param ComposerFailed $event
     */
    public function onComposerFailed(ComposerFailed $event) {
        $this->progress->setFailed($event->exception->getMessage());
        $this->progress->save();
        $this->memory->finish();
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
