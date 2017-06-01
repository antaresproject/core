<?php

declare(strict_types = 1);

namespace Antares\Extension\Processors;

use Antares\Extension\Events\ComposerFailed;
use Antares\Extension\Events\ComposerSuccess;
use Illuminate\Events\Dispatcher;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Model\Operation;
use Antares\Extension\Composer\Handler as ComposerHandler;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Composer
{

    /**
     * @var ComposerHandler
     */
    protected $composerHandler;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Composer constructor.
     * @param ComposerHandler $composerHandler
     * @param Dispatcher $dispatcher
     */
    public function __construct(ComposerHandler $composerHandler, Dispatcher $dispatcher)
    {
        $this->composerHandler = $composerHandler;
        $this->dispatcher      = $dispatcher;
    }

    /**
     * Run the operation for composer.
     *
     * @param OperationHandlerContract $handler
     * @param array $extensionsNames
     * @return mixed
     * @throws \Exception
     */
    public function run(OperationHandlerContract $handler, array $extensionsNames)
    {
        if (count($extensionsNames) === 0) {
            return $handler->operationInfo(new Operation('No extensions to install. Skipping composer.'));
        }
        $required = config('components.required');
        $modules  = [];
        if (!empty($required)) {
            foreach ($extensionsNames as $extension) {

                foreach ($required as $name) {
                    if (starts_with($extension, $name)) {
                        continue;
                    }
                    if (!in_array($extension, $modules)) {
                        array_push($modules, $extension);
                    }
                }
            }
        } else {
            $modules = $extensionsNames;
        }

        $names   = implode(' ', $modules);
        $command = 'composer require ' . $names . ' --no-progress';

        try {
            $handler->operationInfo(new Operation('Running composer command.'));

            $process = $this->composerHandler->run($command, function(Process $process, $type, $buffer) use($handler) {


                $handler->operationInfo(new Operation($buffer));
            });

            if (!$process->isSuccessful()) {
                throw new ExtensionException($process->getErrorOutput());
            }

            $this->dispatcher->fire(new ComposerSuccess($command));

            return $handler->operationInfo(new Operation('Composer command has been finished.'));
        } catch (\Exception $e) {
            Log::error($e);
            $this->dispatcher->fire(new ComposerFailed($command, $e));

            return $handler->operationFailed(new Operation($e->getMessage()));
        }
    }

}
