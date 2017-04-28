<?php

namespace Antares\Foundation\Listeners;

use Antares\Extension\Events\Activated;
use Antares\Extension\Contracts\ProgressContract;
use Antares\Extension\Events\ComposerFailed;
use Antares\Extension\Events\Deactivated;
use Antares\Extension\Events\Failed;
use Antares\Extension\Events\Installed;
use Antares\Extension\Events\Uninstalled;
use Antares\Extension\ExtensionProgress;
use Illuminate\Events\Dispatcher;
use Log;

class AfterExtensionOperation
{

    /**
     * @var ProgressContract
     */
    protected $progress;

    /**
     * AfterExtensionOperation constructor.
     * @param ExtensionProgress $progress
     */
    public function __construct(ExtensionProgress $progress)
    {
        $this->progress = $progress;
    }

    /**
     * @param Activated $event
     */
    public function onActivated(Activated $event)
    {
        $this->progress->advanceStep();

        if ($this->progress->isFinished()) {
            $name = $event->extension->getFriendlyName();

            $this->progress->setSuccessMessage('The package [' . $name . '] has been successfully activated.');
        }
    }

    /**
     * @param Deactivated $event
     */
    public function onDeactivated(Deactivated $event)
    {
        $this->progress->advanceStep();

        if ($this->progress->isFinished()) {
            $name = $event->extension->getFriendlyName();

            $this->progress->setSuccessMessage('The package [' . $name . '] has been successfully deactivated.');
        }
    }

    /**
     * @param Installed $event
     */
    public function onInstalled(Installed $event)
    {
        $this->progress->advanceStep();

        if ($this->progress->isFinished()) {
            $name = $event->extension->getFriendlyName();

            $this->progress->setSuccessMessage('The package [' . $name . '] has been successfully installed.');
        }
    }

    /**
     * @param Uninstalled $event
     */
    public function onUninstalled(Uninstalled $event)
    {
        $this->progress->advanceStep();

        if ($this->progress->isFinished()) {
            $name = $event->extension->getFriendlyName();

            $this->progress->setSuccessMessage('The package [' . $name . '] has been successfully uninstalled.');
        }
    }

    /**
     * @param Failed $event
     */
    public function onFailed(Failed $event)
    {
        $name = $event->extension->getFriendlyName();

        $this->progress->setFailed('An error occurs while doing operation of the [' . $name . '] package.');
        Log::error($event->exception);
        Log::critical($event->exception->getMessage());
    }

    /**
     * @param ComposerFailed $event
     */
    public function onComposerFailed(ComposerFailed $event)
    {
        $this->progress->setFailed($event->exception->getMessage());

        Log::critical($event->exception->getMessage());
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe(Dispatcher $events)
    {
        if (!app()->make('antares.installed')) {
            return;
        }

        $events->listen(
                Activated::class, static::class . '@onActivated'
        );

        $events->listen(
                Installed::class, static::class . '@onInstalled'
        );

        $events->listen(
                Deactivated::class, static::class . '@onDeactivated'
        );

        $events->listen(
                Uninstalled::class, static::class . '@onUninstalled'
        );

        $events->listen(
                Failed::class, static::class . '@onFailed'
        );

        $events->listen(
                ComposerFailed::class, static::class . '@onComposerFailed'
        );
    }

}
