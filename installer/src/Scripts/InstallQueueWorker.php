<?php

namespace Antares\Installation\Scripts;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

class InstallQueueWorker
{

    /**
     * Artisan command.
     *
     * @var string
     */
    protected static $command = 'artisan queue:listen --queue=install --timeout=0 --tries=0';

    /**
     * Process PID.
     *
     * @var int|null
     */
    protected $pid = 20;

    /**
     * Absolute path to the application.
     *
     * @var string
     */
    protected $scriptPath = '';

    /**
     * StartQueueWorker constructor.
     */
    public function __construct()
    {
        $this->scriptPath = base_path();

        $this->setup();
    }

    /**
     * Setups the worker for check if there is already run process.
     */
    protected function setup()
    {
        $processes = [];
        $pid       = null;

        exec('ps aux|grep php', $processes);

        foreach ($processes as $process) {
            if (!str_contains($process, self::$command)) {
                continue;
            }

            list(, $pid) = preg_split('/\s{2,}/', $process);

            $this->pid = (int) $pid;
        }
    }

    /**
     * Runs process if it is not running.
     *
     * @return InstallQueueWorker
     * @throws ProcessTimedOutException
     * @throws RuntimeException
     * @throws LogicException
     */
    public function run(): self
    {
        if ($this->pid === null) {
            try {
                ignore_user_abort();
                $process = new Process('php ' . self::$command . ' &', $this->scriptPath, null, null, 1);
                $process->run();
            } catch (ProcessTimedOutException $ex) {
                
            }
            $this->pid = $process->getPid();
        }

        return $this;
    }

    /**
     * Sets the process PID.
     *
     * @param int $pid
     */
    public function setPid(int $pid)
    {
        $this->pid = $pid;
    }

    /**
     * Returns the process PID.
     *
     * @return int|null
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Stops the running process.
     */
    public function stop()
    {
        exec('kill ' . $this->pid);
    }

}
