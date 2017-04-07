<?php

declare(strict_types=1);

namespace Antares\Extension\Composer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Closure;
use Illuminate\Support\Str;

class Handler {

    /**
     * @var array
     */
    protected $commandParameters;

    /**
     * Handler constructor.
     * @param array $commandParameters
     */
    public function __construct(array $commandParameters = []) {
        if( env('COMPOSER_HOME') === null) {
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
    public function run(string $command, Closure $callback = null) : Process {
        set_time_limit(0);
        gc_disable();

		$process = new Process( $this->buildCommand($command) );
		$process->setWorkingDirectory( base_path() );
		$process->setTimeout(null);

		try {
			$process->mustRun(function($type, $buffer) use($callback, $process) {
				if( empty($buffer) ) {
					return null;
				}

                $buffer = preg_replace("/[\x08]/mu", "\r\n", $buffer);

				if($buffer && $callback instanceof Closure) {
					$callback($process, $type, $buffer);
				}
			});
		}
		catch(ProcessFailedException $e) {
			throw $e;
		}

		return $process;
    }

    /**
     * @param string $command
     * @return string
     */
    protected function buildCommand(string $command) : string {
        foreach($this->commandParameters as $parameter) {
            if( ! Str::contains($command, $parameter) ) {
                $command .= ' ' . $parameter;
            }
        }

        return $command;
    }

}
