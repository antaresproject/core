<?php

declare(strict_types=1);

namespace Antares\Extension\Processors;

use Antares\Extension\Contracts\ProgressContract;
use Antares\Installation\Repository\Installation;
use Antares\Installation\Scripts\InstallQueueWorker;
use Antares\Memory\MemoryManager;
use Antares\Memory\Provider;
use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Progress implements ProgressContract {

    /**
     * Installation repository.
     *
     * @var Installation
     */
    protected $installation;

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
     * @var int|null
     */
    protected $pid;

    /**
     * Progress constructor.
     * @param Installation $installation
     * @param InstallQueueWorker $installQueueWorker
     * @throws \InvalidArgumentException
     */
    public function __construct(Installation $installation, InstallQueueWorker $installQueueWorker) {
        $this->installation         = $installation;
        $this->installQueueWorker   = $installQueueWorker;

        $this->filePath             = storage_path('extension-operation.txt');
        $this->pid                  = $this->installation->getPid();

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

        if($this->pid) {
            $this->installation->setPid( (int) $this->pid);
        }

        File::put($this->filePath, '');

        $this->installation->setStarted(true);
        $this->installation->save();
    }

    /**
     * Resets the progress state.
     */
    public function reset() {
        $this->installation->forget();
        $this->installation->save();

        if($this->pid) {
            $this->installQueueWorker->stop();
        }

        File::delete($this->filePath);
    }

    protected function startQueueWorker() {
        try {
            $this->pid = $this->installQueueWorker->run()->getPid();
        }
        catch(\Exception $e) {
            $this->setFailed($e->getMessage());
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
        $this->installation->setFailed(true);
        $this->installation->setFailedMessage($message);
    }

    /**
     * Sets progress as finished.
     */
    public function setFinished() {
        File::delete($this->filePath);

        $this->installation->setStarted(false);
        $this->installation->setFinished(true);

        $this->installation->save();
    }

    /**
     * Determines if the progress has been finished.
     *
     * @return bool
     */
    public function isFinished() : bool {
        return $this->installation->finished();
    }

    /**
     * Determines if the progress is running.
     *
     * @return bool
     */
    public function isRunning() : bool {
        return $this->installation->progressing();
    }

    /**
     * @return bool
     */
    public function isFailed() : bool {
        return $this->installation->failed();
    }

    /**
     * @return string
     */
    public function getFailedMessage() : string {
        return (string) $this->installation->getFailedMessage();
    }

}
