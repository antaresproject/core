<?php

declare(strict_types=1);

namespace Antares\Extension\Contracts;

interface ProgressContract {

    /**
     * Returns the file system path of the output console.
     *
     * @return string
     */
    public function getFilePath() : string;

    /**
     * Starts the progress state.
     */
    public function start();

    /**
     * Returns the installation console output.
     *
     * @return string
     */
    public function getOutput() : string;

    /**
     * Determines if the progress has been finished.
     *
     * @return bool
     */
    public function isFinished() : bool;

    /**
     * Determines if the progress is running.
     *
     * @return bool
     */
    public function isRunning() : bool;

}
