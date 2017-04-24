<?php

namespace Antares\Installation\Scripts;

use Illuminate\Support\Arr;
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
    protected $pid = null;

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
        $pid = null;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return false;
        }
        $processes = $this->runCommand('ps aux|grep php');

        foreach ($processes as $process) {
            if (!str_contains($process, self::$command)) {
                continue;
            }

            list(, $pid) = preg_split('/\s{2,}/', $process);

            $this->pid = (int) $pid;
        }
    }

    /**
     * Run command if is not in during tests.
     *
     * @param string $command
     * @return array The command output
     */
    protected function runCommand(string $command): array
    {
        $processes = [];

        if (PHP_SAPI === 'cli') {
            $firstArgument = Arr::get($_SERVER, 'argv.0', '');

            if (strpos($firstArgument, 'phpunit') !== FALSE) {
                return $processes;
            }
        }

        exec($command, $processes);

        return $processes;
    }

    /**
     * Runs process if it is not running.
     *
     * @return InstallQueueWorker
     * @throws RuntimeException
     * @throws LogicException
     */
    public function run(): self
    {
        if ($this->pid === null) {
            ignore_user_abort();

            try {
                $process = new Process('nohup php ' . self::$command . ' &', $this->scriptPath, null, null, 1);
                $process->run();

                $this->pid = $process->getPid();
            } catch (ProcessTimedOutException $e) {
                // Do nothing.
            }
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
