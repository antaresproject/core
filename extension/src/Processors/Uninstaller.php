<?php

declare(strict_types=1);

namespace Antares\Extension\Processors;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Events\Failed;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Model\Operation;
use Antares\Extension\Events\Uninstalled;
use Antares\Extension\Events\Uninstalling;
use Antares\Extension\Repositories\ExtensionsRepository;
use Antares\Extension\Composer\Handler as ComposerHandler;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Antares\Console\Kernel;
use Antares\Publisher\AssetManager;
use Antares\Publisher\MigrateManager;
use Illuminate\Support\Str;
use Artisan;

class Uninstaller extends AbstractOperation {

    /**
     * @var ComposerHandler
     */
    protected $composerHandler;

    /**
     * @var MigrateManager
     */
    protected $migrateManager;

    /**
     * @var AssetManager
     */
    protected $assetManager;

    /**
     * @var ExtensionsRepository
     */
    protected $extensionsRepository;

    /**
     * Uninstaller constructor.
     * @param ComposerHandler $composerHandler
     * @param Container $container
     * @param Dispatcher $dispatcher
     * @param Kernel $kernel
     * @param ExtensionsRepository $extensionsRepository
     */
    public function __construct(
        ComposerHandler $composerHandler,
        Container $container,
        Dispatcher $dispatcher,
        Kernel $kernel,
        ExtensionsRepository $extensionsRepository
    )
    {
        parent::__construct($container, $dispatcher, $kernel);

        $this->composerHandler      = $composerHandler;
        $this->extensionsRepository = $extensionsRepository;

        $this->migrateManager       = $container->make('antares.publisher.migrate');
        $this->assetManager         = $container->make('antares.publisher.asset');
    }

    /**
     * The Run operation.
     *
     * @param OperationHandlerContract $handler
     * @param ExtensionContract $extension
     * @param array $flags
     * @return mixed
     * @throws \Exception
     */
    public function run(OperationHandlerContract $handler, ExtensionContract $extension, array $flags = []) {
        try {
            $name = $extension->getPackage()->getName();

            $handler->operationInfo(new Operation('Uninstalling the [' . $name . '] extension.'));

            $this->dispatcher->fire(new Uninstalling($extension));
            $this->migrateManager->uninstall($name);
            $this->assetManager->delete(str_replace('/', '_', $name));

            $this->extensionsRepository->save($extension, [
                'status'    => ExtensionContract::STATUS_AVAILABLE,
                'options'   => [],
            ]);

            if(in_array('purge', $flags, true)) {
                $command = 'composer remove ' . $name;

                $process = $this->composerHandler->run($command, function($process, $type, $buffer) use($handler) {
                    if(Str::contains($buffer, 'Error Output')) {
                        throw new ExtensionException($buffer);
                    }

                    $handler->operationInfo(new Operation($buffer));
                });

                if( ! $process->isSuccessful() ) {
                    throw new ExtensionException($process->getErrorOutput());
                }
            }

            $this->dispatcher->fire(new Uninstalled($extension));

            $operation = new Operation('The package [' . $name . '] has been successfully uninstalled.');

            return $handler->operationSuccess($operation);
        }
        catch(\Exception $e) {
            $this->dispatcher->fire(new Failed($extension, $e));

            return $handler->operationFailed(new Operation($e->getMessage()));
        }
    }

}
