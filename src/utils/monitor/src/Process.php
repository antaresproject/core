<?php

namespace Antares\Monitor;

use Illuminate\Contracts\Support\Arrayable;

class Process implements Arrayable {

    /**
     * Process command.
     *
     * @var string
     */
    protected $command;

    /**
     * Info about running process.
     *
     * @var ProcessInfo|null
     */
    protected $info;

    /**
     * Process constructor.
     * @param string $command
     * @param ProcessInfo|null $info
     */
    public function __construct(string $command, ProcessInfo $info = null) {
        $this->command  = $command;
        $this->info     = $info;
    }

    /**
     * Returns the process command.
     *
     * @return string
     */
    public function getCommand() : string {
        return $this->command;
    }

    /**
     * Sets info to the process.
     *
     * @param ProcessInfo $info
     */
    public function setInfo(ProcessInfo $info) {
        $this->info = $info;
    }

    /**
     * Returns the process info if the process is running.
     *
     * @return ProcessInfo|null
     */
    public function getInfo() : ?ProcessInfo {
        return $this->info;
    }

    /**
     * Checks if the process is running.
     *
     * @return bool
     */
    public function isRunning() : bool {
        return $this->info instanceof ProcessInfo;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() : array {
        return [
            'command'   => $this->getCommand(),
            'running'   => $this->isRunning(),
            'info'      => $this->getInfo() ? $this->getInfo()->toArray() : null,
        ];
    }

    /**
     * Kill running process.
     */
    public function kill() {
        if($this->isRunning() && $pid = $this->getInfo()->getPid()) {
            exec('kill ' . $pid);
        }
    }
}