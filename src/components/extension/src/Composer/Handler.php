<?php

declare(strict_types = 1);

namespace Antares\Extension\Composer;

use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Closure;
use Exception;

class Handler
{

    /**
     * @var array
     */
    protected $commandParameters;

    /**
     * Handler constructor.
     * @param array $commandParameters
     */
    public function __construct(array $commandParameters = [])
    {
        if (env('COMPOSER_HOME') === null) {
            putenv('COMPOSER_HOME=' . base_path());
        }

        $this->commandParameters = $commandParameters;
    }

    /**
     * Runs the command.
     *
     * @param string $command
     * @param Closure|null $callback
     * @return Process
     * @throws \Exception
     */
    public function run(string $command, Closure $callback = null): Process
    {
        set_time_limit(0);
        gc_disable();

        $process = new Process($this->buildCommand($command));
        $process->setWorkingDirectory(base_path());
        $process->setTimeout(null);

        try {
            $process->mustRun(function($type, $buffer) use($callback, $process) {
                if (empty($buffer)) {
                    return null;
                }

                if ($callback instanceof Closure) {
                    $callback($process, $type, $buffer);
                }
            });
        } catch (Exception $e) {
            Log::error($e);
            throw $e;
        }

        return $process;
    }

    /**
     * @param string $command
     * @return string
     */
    protected function buildCommand(string $command): string
    {
        foreach ($this->commandParameters as $parameter) {
            if (!Str::contains($command, $parameter)) {
                $command .= ' ' . $parameter;
            }
        }

        return $command;
    }

}
