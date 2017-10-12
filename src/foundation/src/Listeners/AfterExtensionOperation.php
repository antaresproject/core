<?php

namespace Antares\Foundation\Listeners;

use Antares\Events\Compontents\ComponentActivated;
use Antares\Events\Compontents\ComponentDeactivated;
use Antares\Events\Compontents\ComponentInstallationFailed;
use Antares\Events\Compontents\ComponentInstalled;
use Antares\Events\Compontents\ComponentUninstalled;
use Antares\Events\Composer\Failed;
use Antares\Extension\Contracts\ProgressContract;
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
     * @param ComponentActivated $event
     */
    public function onActivated(ComponentActivated $event)
    {
        $this->progress->advanceStep();

        if ($this->progress->isFinished()) {
            $name = $event->component->getFriendlyName();

            $this->progress->setSuccessMessage('The package [' . $name . '] has been successfully activated.');
        }
    }

    /**
     * @param ComponentDeactivated $event
     */
    public function onDeactivated(ComponentDeactivated $event)
    {
        $this->progress->advanceStep();

        if ($this->progress->isFinished()) {
            $name = $event->component->getFriendlyName();

            $this->progress->setSuccessMessage('The package [' . $name . '] has been successfully deactivated.');
        }
    }

    /**
     * @param ComponentInstalled $event
     */
    public function onInstalled(ComponentInstalled $event)
    {
        $this->progress->advanceStep();

        if ($this->progress->isFinished()) {
            $name = $event->component->getFriendlyName();

            $this->progress->setSuccessMessage('The package [' . $name . '] has been successfully installed.');
        }
    }

    /**
     * @param ComponentUninstalled $event
     */
    public function onUninstalled(ComponentUninstalled $event)
    {
        $this->progress->advanceStep();

        if ($this->progress->isFinished()) {
            $name = $event->component->getFriendlyName();

            $this->progress->setSuccessMessage('The package [' . $name . '] has been successfully uninstalled.');
        }
    }

    /**
     * @param ComponentInstallationFailed $event
     */
    public function onFailed(ComponentInstallationFailed $event)
    {
        $name = $event->component->getFriendlyName();

        $this->progress->setFailed('An error occurs while doing operation of the [' . $name . '] package.');
        Log::error($event->exception);
        Log::critical($event->exception->getMessage());
    }

    /**
     * @param Failed $event
     */
    public function onComposerFailed(Failed $event)
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
                ComponentActivated::class, static::class . '@onActivated'
        );

        $events->listen(
                ComponentInstalled::class, static::class . '@onInstalled'
        );

        $events->listen(
                ComponentDeactivated::class, static::class . '@onDeactivated'
        );

        $events->listen(
                ComponentUninstalled::class, static::class . '@onUninstalled'
        );

        $events->listen(
            ComponentInstallationFailed::class, static::class . '@onFailed'
        );

        $events->listen(
                Failed::class, static::class . '@onComposerFailed'
        );
    }

}
