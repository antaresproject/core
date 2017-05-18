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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Installation\Scripts;

use Symfony\Component\Process\Pipes\WindowsPipes;
use Symfony\Component\Process\Process;
use Exception;

class WindowsProcessMonitor extends AbstractProcessMonitor
{

    /**
     * Pids list
     *
     * @var array 
     */
    protected $pids = [];

    /**
     * Checks whether process started
     * 
     * @param String $name
     * @return boolean
     */
    private function isProcessStarted()
    {
        $pids = $this->getPids();
        return count($pids) >= count($this->commands);
    }

    /**
     * Gets process pids
     * 
     * @return array
     */
    private function getPids()
    {
        $processes = [];
        exec('tasklist /FI "IMAGENAME eq php*" /fo csv 2>NUL', $processes);
        $pids      = [];
        foreach ($processes as $process) {
            if (str_contains($process, 'php.exe')) {
                @list($name, $pid) = explode(',', $process);
                $pids[] = trim($pid, '"');
            }
        }
        return $this->pids = $pids;
    }

    protected function killPids()
    {
        if (empty($this->pids)) {
            return false;
        }
    }

    /**
     * Process single command
     * 
     * @param String $command
     * @return boolean
     */
    public function process($command)
    {
        $started = $this->isProcessStarted();
        if ($started) {
            return true;
        }
        $this->killPids();
        $function = function() use($command) {
            $process      = new Process($command);
            $processPipes = WindowsPipes::create($process, null);
            $descriptors  = $processPipes->getDescriptors();
            $options      = [
                "suppress_errors" => true,
                "binary_pipes"    => true,
                "bypass_shell"    => true,
            ];
            $cwd          = getcwd();
            $env          = null;
            proc_open('cmd /V:ON /E:ON /D /C "(' . $command . ')"', $descriptors, $processPipes->pipes, $cwd, $env, $options);
        };
        try {
            call_user_func($function, $command);
        } catch (Exception $ex) {
            
        }

        return true;
    }

}
