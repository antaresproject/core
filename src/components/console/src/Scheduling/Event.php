<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Console\Scheduling;

use Illuminate\Console\Scheduling\Event as SupportEvent;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Process\Process;

class Event extends SupportEvent
{

    /**
     * instance of process
     *
     * @var Process
     */
    protected $process;

    /**
     * Run the command in the foreground.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    protected function runCommandInForeground(Container $container)
    {
        $this->callBeforeCallbacks($container);
        $process       = new Process(trim($this->buildCommand(), '& '), base_path(), null, null, null);
        $process->run();
        $this->process = $process;
        $this->callAfterCallbacks($container);
    }

    /**
     * process getter
     * 
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * Build the command string.
     *
     * @return string
     */
    public function buildCommand()
    {
        $output = ProcessUtils::escapeArgument($this->output);

        $redirect = $this->shouldAppendOutput ? ' >> ' : ' > ';

        if ($this->withoutOverlapping) {
            $command = '(touch ' . $this->mutexPath() . '; ' . $this->command . '; rm ' . $this->mutexPath() . ')' . $redirect . $output . ' 2>&1 &';
        } else {
            $command = $this->command;
        }

        return $this->user ? 'sudo -u ' . $this->user . ' ' . $command : $command;
    }

}
