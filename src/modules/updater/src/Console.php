<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater;

use Symfony\Component\Process\Process;

class Console
{

    /**
     * Run a command in the shell.
     *
     * @param $command
     * @param int   $timeoutInSeconds
     * @param array $env
     *
     * @return bool|string
     */
    public function run($command, $timeoutInSeconds = 60, array $env = null)
    {
        $process = new Process($command);

        $process->setTimeout($timeoutInSeconds);

        if ($env != null) {
            $process->setEnv($env);
        }

        $process->run();

        if ($process->isSuccessful()) {
            return true;
        } else {
            return $process->getErrorOutput();
        }
    }

}
