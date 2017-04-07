<?php

declare(strict_types=1);

namespace Antares\Installation;

use Antares\Extension\Contracts\ProgressContract;
use Antares\Extension\Jobs\BulkExtensionsBackgroundJob;
use Antares\Installation\Scripts\InstallQueueWorker;
use Antares\Memory\MemoryManager;
use Antares\Memory\Provider;
use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Artisan;

class Progress implements ProgressContract {

    /**
     * Memory provider instance.
     *
     * @var Provider
     */
    protected $memory;

    /**
     * Install queue worker instance.
     *
     * @var InstallQueueWorker
     */
    protected $installQueueWorker;

    /**
     * File path of the console output.
     *
     * @var string
     */
    protected $filePath;

    /**
     * Count of steps.
     *
     * @var int
     */
    protected $stepsCount = 0;

    /**
     * Count of completed steps.
     *
     * @var int
     */
    protected $completedStepsCount;

    /**
     * Running indicator.
     *
     * @var bool
     */
    protected $isRunning;

    /**
     * @var int|null
     */
    protected $pid;

    /**
     * @var bool
     */
    protected $failed;

    /**
     * Progress constructor.
     * @param MemoryManager $memoryManager
     * @param InstallQueueWorker $installQueueWorker
     */
    public function __construct(MemoryManager $memoryManager, InstallQueueWorker $installQueueWorker) {
        $this->memory               = $memoryManager->make('primary');
        $this->installQueueWorker   = $installQueueWorker;
        $this->filePath             = storage_path('installation.txt');

        $this->memory->getHandler()->initiate();

        // Steps are the sum of extensions and composer command.
        $this->stepsCount           = (int) count( $this->memory->get('app.installation.components', []) ) + 1;
        $this->completedStepsCount  = (int) $this->memory->get('app.installation.completed', 0);
        $this->isRunning            = (bool) $this->memory->get('app.installing', false);
        $this->pid                  = $this->memory->get('app.installation.pid');
        $this->failed               = (bool) $this->memory->get('app.installation.failed', false);

        if($this->pid) {
            $this->installQueueWorker->setPid( (int) $this->pid);
        }
    }

    /**
     * Returns the file system path of the output console.
     *
     * @return string
     */
    public function getFilePath() : string {
        return $this->filePath;
    }

    /**
     * Starts the progress state.
     */
    public function start() {
        $this->startQueueWorker();
        $this->memory->put('app.installation.pid', $this->pid);

        if( ! $this->isRunning()) {
            File::put($this->filePath, '');

            $this->completedStepsCount = 0;
            $this->memory->put('app.installation.completed', $this->completedStepsCount);

            $this->isRunning = true;
            $this->memory->put('app.installing', $this->isRunning);

            $this->runQueue();
        }
    }

    protected function startQueueWorker() {
        try {
            $this->pid = $this->installQueueWorker->run()->getPid();
        }
        catch(\Exception $e) {
            $this->setFailed($e->getMessage());
            $this->save();
        }
    }

    protected function runQueue() {
        $extensions = $this->memory->get('app.installation.components');

        $operationClasses = [
            \Antares\Extension\Processors\Installer::class,
            \Antares\Extension\Processors\Activator::class,
        ];

        $installJob = new BulkExtensionsBackgroundJob($extensions, $operationClasses, $this->getFilePath());
        $installJob->onQueue('install');

        dispatch($installJob);
    }

    /**
     * Stops the progress state.
     */
    public function stop() {
        if( $this->isRunning() ) {
            $this->reset();
        }
    }

    /**
     * Resets the progress state.
     */
    public function reset() {
        $this->memory->forget('app.installation.components');
        $this->stepsCount = 0;

        $this->isRunning = false;
        $this->memory->put('app.installing', $this->isRunning);

        $this->failed = false;
        $this->memory->put('app.installation.failed', $this->failed);
        $this->memory->put('app.installation.failed_message', '');

        if($this->pid) {
            $this->installQueueWorker->stop();
            $this->pid = null;
            $this->memory->put('app.installation.pid', $this->pid);
        }

        File::delete($this->filePath);

        //Artisan::call('queue:flush');
    }

    /**
     * Returns the installation console output.
     *
     * @return string
     */
    public function getOutput() : string {
        try {
            $content = File::get($this->filePath);

            $content = preg_replace("/[\x08]+/", "\r\n", $content);
            $content = preg_replace("/[\r\n]+/", "\n", $content);

            return $content;
        }
        catch(FileNotFoundException $e) {
            return '';
        }
    }

    /**
     * Returns the count of steps.
     *
     * @return int
     */
    public function getStepsCount() : int {
        return $this->stepsCount;
    }

    /**
     * Increments completed steps.
     */
    public function advanceStep() {
        $this->memory->put('app.installation.completed', ++$this->completedStepsCount);
    }

    /**
     * Returns the percentage of installation progress (from 0 to 100).
     *
     * @return int
     */
    public function getPercentageProgress() : int {
        if($this->stepsCount === 0) {
            return 0;
        }

        return (int) round(($this->completedStepsCount / $this->stepsCount) * 100, 0);
    }

    /**
     * Determines if the progress has been finished.
     *
     * @return bool
     */
    public function isFinished() : bool {
        return $this->memory->get('app.installed', false) || $this->completedStepsCount === $this->stepsCount;
    }

    /**
     * Determines if the progress is running.
     *
     * @return bool
     */
    public function isRunning() : bool {
        return $this->isRunning;
    }

    /**
     * Saves the memory.
     */
    public function save() {
        $this->memory->finish();
    }

    /**
     * Sets the progress as failed.
     *
     * @param string $message
     */
    public function setFailed(string $message) {
        $this->failed = true;
        $this->memory->put('app.installation.failed', $this->failed);
        $this->memory->put('app.installation.failed_message', $message);

        $this->isRunning = false;
        $this->memory->put('app.installing', $this->isRunning);
    }

    /**
     * @return bool
     */
    public function isFailed() : bool {
        return $this->failed;
    }

    /**
     * @return string
     */
    public function getFailedMessage() : string {
        return (string) $this->memory->get('app.installation.failed_message', '');
    }

}
