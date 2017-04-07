<?php

declare(strict_types=1);

namespace Antares\Extension\Processors;

use Antares\Extension\Contracts\ProgressContract;
use Antares\Installation\Scripts\InstallQueueWorker;
use Antares\Memory\MemoryManager;
use Antares\Memory\Provider;
use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

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
        $this->filePath             = storage_path('extension-operation.txt');
        $this->isRunning            = (bool) $this->memory->get('app.extension.installing', false);

        $this->pid                  = $this->memory->get('app.extension.pid');
        $this->failed               = (bool) $this->memory->get('app.extension.failed', false);

        if($this->pid) {
            $this->installQueueWorker->setPid($this->pid);
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
        $this->memory->put('app.extension.pid', $this->pid);
        File::put($this->filePath, '');

        $this->isRunning = true;
        $this->memory->put('app.extension.installing', $this->isRunning);
    }

    /**
     * Resets the progress state.
     */
    public function reset() {
        $this->memory->forget('app.installation.components');
        $this->isRunning = false;
        $this->memory->put('app.extension.installing', $this->isRunning);

        $this->failed = false;
        $this->memory->put('app.extension.failed', $this->failed);
        $this->memory->put('app.extension.failed_message', '');

        if($this->pid) {
            $this->installQueueWorker->stop();
            $this->pid = null;
            $this->memory->put('app.extension.pid', $this->pid);
        }

        File::delete($this->filePath);
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

    /**
     * Adds content to the output file.
     *
     * @param string $content
     */
    public function addToOutput(string $content) {
        File::append($this->filePath, $content);
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
     * Sets the progress as failed.
     *
     * @param string $message
     */
    public function setFailed(string $message) {
        $this->failed = true;
        $this->memory->put('app.extension.failed', $this->failed);
        $this->memory->put('app.extension.failed_message', $message);

        $this->isRunning = false;
        $this->memory->put('app.extension.installing', $this->isRunning);
    }

    /**
     * Sets progress as finished.
     */
    public function setFinished() {
        File::delete($this->filePath);

        $this->isRunning = false;
        $this->memory->put('app.extension.installing', $this->isRunning);
    }

    /**
     * Determines if the progress has been finished.
     *
     * @return bool
     */
    public function isFinished() : bool {
        return ! $this->isRunning;
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
     * Saves the progress in the memory.
     */
    public function save() {
        $this->memory->finish();
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
        return (string) $this->memory->get('app.extension.failed_message', '');
    }

}
