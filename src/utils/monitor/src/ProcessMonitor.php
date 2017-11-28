<?php

namespace Antares\Monitor;

use Antares\Support\Str;

class ProcessMonitor {

    /**
     * Watched processes list.
     *
     * @var Process[]
     */
    protected $processes = [];

    /**
     * IF check method has been executed.
     *
     * @var bool
     */
    protected $checked = false;

    /**
     * Adds process to the watched processes list.
     *
     * @param string $command
     */
    public function watch(string $command) : void {
        $this->processes[ $command ] = new Process($command);
    }

    /**
     * Checks processes.
     */
    public function check() : void {
        $processesInfo = $this->getProcessesInfo();

        foreach($this->processes as $process) {
            foreach($processesInfo as $processInfo) {
                if( Str::startsWith($processInfo->getCommand(), $process->getCommand()) ) {
                    $process->setInfo($processInfo);
                    break 2;
                }
            }
        }

        $this->checked = true;
    }

    /**
     * Returns list of watched processes.
     *
     * @return Process[]
     */
    public function getWatchedProcesses() : array {
        if(! $this->checked) {
            $this->check();
        }

        return array_values($this->processes);
    }

    /**
     * Returns list of running processes.
     *
     * @return ProcessInfo[]
     */
    public function getProcessesInfo() : array {
        $processIds = [];
        $processes  = [];
        $data       = [];

        exec('pgrep php', $processIds);
        exec('ps ahxwwo pid:1,command:1 | grep php | grep -v grep | grep -v emacs', $processes);

        foreach($processes as $process) {
            $pid = explode(' ', trim($process), 2)[0];

            if( in_array($pid, $processIds) ) {
                $data[] = new ProcessInfo($process);
            }
        }

        return $data;
    }

    /**
     * Returns process info about command.
     *
     * @param string $command
     * @return Process
     */
    public function getProcessByCommand(string $command) : Process {
        if( ! array_key_exists($command, $this->processes) ) {
            $this->watch($command);
        }

        foreach($this->getWatchedProcesses() as $process) {
            if($process->getCommand() === $command) {
                return $process;
            }
        }
    }

    /**
     * @param string $command
     */
    public function run(string $command) {
        if( Str::startsWith($command, 'artisan') ) {
            $command = str_replace('artisan ', '', $command);
        }

        ignore_user_abort();

        $log        = storage_path('logs' . DIRECTORY_SEPARATOR . snake_case(Str::slug($command)) . '.log');
        $rootPath   = base_path();

        shell_exec("cd $rootPath && php artisan $command >> " . $log . " &");
    }

}