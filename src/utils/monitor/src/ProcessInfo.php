<?php

namespace Antares\Monitor;

use Illuminate\Contracts\Support\Arrayable;

class ProcessInfo implements Arrayable {

    /**
     * Process ID.
     *
     * @var int
     */
    protected $pid;

    /**
     * Process file path.
     *
     * @var string
     */
    protected $path;

    /**
     * Process command without file path.
     *
     * @var string
     */
    protected $command;

    /**
     * ProcessInfo constructor.
     * @param string $processLine
     */
    public function __construct(string $processLine) {
        list($this->pid, $this->path, $this->command) = explode(' ', $processLine, 3);
    }

    /**
     * Returns process command without file path.
     *
     * @return string
     */
    public function getCommand() : string {
        return $this->command;
    }

    /**
     * Returns process command file path.
     *
     * @return string
     */
    public function getPath() : string {
        return $this->path;
    }

    /**
     * Returns process ID.
     *
     * @return int
     */
    public function getPid() : int {
        return (int) $this->pid;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() : array {
        return [
            'command'   => $this->getCommand(),
            'path'      => $this->getPath(),
            'pid'       => $this->getPid(),
        ];
    }
}
