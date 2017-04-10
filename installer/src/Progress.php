<?php

declare(strict_types=1);

namespace Antares\Installation;

use Antares\Extension\Contracts\ProgressContract;
use Antares\Extension\Jobs\BulkExtensionsBackgroundJob;
use Antares\Extension\Repositories\ComponentsRepository;
use Antares\Installation\Scripts\InstallQueueWorker;
use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use \Antares\Installation\Repository\Installation as InstallationRepository;

class Progress implements ProgressContract {

    /**
     * Installation repository.
     *
     * @var InstallationRepository
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
     * @param InstallationRepository $installation
     * @param InstallQueueWorker $installQueueWorker
     * @throws \InvalidArgumentException
     */
    public function __construct(InstallationRepository $installation, InstallQueueWorker $installQueueWorker) {
        $this->installation         = $installation;
        $this->installQueueWorker   = $installQueueWorker;
        $this->filePath             = storage_path('installation.txt');

        $this->pid = $this->installation->getPid();

        if($this->pid) {
            $this->installQueueWorker->setPid( (int) $this->pid);
        }
    }

    /**
     * @param array $components
     */
    public function setComponents(array $components) {
        // Steps are the sum of extensions and composer command.
        $this->installation->setCustom('steps', 0 + count($components));
        $this->installation->setCustom('components', $components);
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

        if( ! $this->installation->failed() && ! $this->installation->progressing() ) {
            File::put($this->filePath, '');

            $this->installation->forgetCustom('steps_completed');
            $this->installation->setStarted(true);

            $this->runQueue();
        }

        $this->installation->save();
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
     * @return ComponentsRepository
     */
    private function getComponentsRepository() {
        return app()->make(ComponentsRepository::class);
    }

    protected function runQueue() {
        $components = (array) $this->installation->getCustom('components');
        $components = $this->getComponentsRepository()->getWithBranches($components);
        $extensions = [];

        foreach($components as $component => $branch) {
            $extensions[] = $component . ':' . $branch;
        }

        $operationClasses = [
            \Antares\Extension\Processors\Installer::class,
            \Antares\Extension\Processors\Activator::class,
        ];

        $installJob = new BulkExtensionsBackgroundJob($extensions, $operationClasses, $this->getFilePath());
        $installJob->onQueue('install');

        dispatch($installJob);

        $this->installation->forgetCustom('components');
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
        $this->installation->forget();
        $this->installation->save();

        if($this->pid) {
            $this->installQueueWorker->stop();
        }

        File::delete($this->filePath);
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
        return (int) $this->installation->getCustom('steps', 0);
    }

    /**
     * @return int
     */
    public function getCompletedSteps() : int {
        return (int) $this->installation->getCustom('steps_completed', 0);
    }

    /**
     * Increments completed steps.
     */
    public function advanceStep() {
        $completed = $this->getCompletedSteps();

        $this->installation->setCustom('steps_completed', ++$completed);

        if($this->isStepsCompleted()) {
            $this->installation->setStarted(false);
            $this->installation->setFinished(true);
        }

        $this->installation->save();
    }

    /**
     * Returns the percentage of installation progress (from 0 to 100).
     *
     * @return int
     */
    public function getPercentageProgress() : int {
        $steps      = $this->getStepsCount();
        $completed  = $this->getCompletedSteps();

        if($steps === 0) {
            return 0;
        }

        $percentage = (int) round(($completed / $steps) * 100, 0);

        return max(min($percentage, 100), 0);
    }

    /**
     * Determines if the progress has been finished.
     *
     * @return bool
     */
    public function isFinished() : bool {
        return $this->getCompletedSteps() === $this->getStepsCount();
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
     * Sets the progress as failed.
     *
     * @param string $message
     */
    public function setFailed(string $message) {
        $this->installation->setFailed(true);
        $this->installation->setFailedMessage($message);
        $this->installation->save();
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
