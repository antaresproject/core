<?php

declare(strict_types=1);

namespace Antares\Extension\Processors;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Events\Failed;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Factories\SettingsFactory;
use Antares\Extension\Model\Operation;
use Antares\Extension\Events\Installed;
use Antares\Extension\Events\Installing;
use Antares\Extension\Repositories\ComponentsRepository;
use Antares\Extension\Repositories\ExtensionsRepository;
use Antares\Extension\Validators\ExtensionValidator;
use Antares\Extension\Composer\Handler as ComposerHandler;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Antares\Console\Kernel;
use Antares\Publisher\AssetManager;
use Antares\Publisher\MigrateManager;
use Illuminate\Support\Str;
use Artisan;

class Installer extends AbstractOperation {

    /**
     * @var ComposerHandler
     */
    protected $composerHandler;

    /**
     * @var ExtensionValidator
     */
    protected $extensionValidator;

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
     * @var SettingsFactory
     */
    protected $settingsFactory;

    /**
     * @var ComponentsRepository
     */
    protected $componentsRepository;

    /**
     * Installer constructor.
     * @param ComposerHandler $composerHandler
     * @param ExtensionValidator $extensionValidator
     * @param Container $container
     * @param Dispatcher $dispatcher
     * @param Kernel $kernel
     * @param ExtensionsRepository $extensionsRepository
     * @param SettingsFactory $settingsFactory
     * @param ComponentsRepository $componentsRepository
     */
    public function __construct(
        ComposerHandler $composerHandler,
        ExtensionValidator $extensionValidator,
        Container $container,
        Dispatcher $dispatcher,
        Kernel $kernel,
        ExtensionsRepository $extensionsRepository,
        SettingsFactory $settingsFactory,
        ComponentsRepository $componentsRepository
    )
    {
        parent::__construct($container, $dispatcher, $kernel);

        $this->composerHandler      = $composerHandler;
        $this->extensionValidator   = $extensionValidator;
        $this->extensionsRepository = $extensionsRepository;
        $this->settingsFactory      = $settingsFactory;
        $this->componentsRepository = $componentsRepository;

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

            $handler->operationInfo(new Operation('Installing the [' . $name . '] extension.'));
            
            $this->dispatcher->fire(new Installing($extension));
            $this->extensionValidator->validateAssetsPath($extension);

            if(in_array('skip-composer', $flags, false) === false) {
                $command = 'composer require ' . $name . ':' .  $this->componentsRepository->getTargetBranch($name);

                $process = $this->composerHandler->run($command, function($process, $type, $buffer) use($handler) {
                    if(Str::contains($buffer, ['Error Output', 'Exception'])) {
                        throw new ExtensionException($buffer);
                    }

                    $handler->operationInfo(new Operation($buffer));
                });

                if( ! $process->isSuccessful() ) {
                    throw new ExtensionException($process->getErrorOutput());
                }
            }

            $this->migrateManager->extension($name);
            $this->assetManager->extension( str_replace('/', '_', $name));
            $this->importSettings($handler, $extension);

            $this->extensionsRepository->save($extension, [
                'status'    => ExtensionContract::STATUS_INSTALLED,
                'options'   => $extension->getSettings()->getData(),
            ]);

            $this->dispatcher->fire(new Installed($extension));

            $operation = new Operation('The package [' . $name . '] has been successfully installed.');

            return $handler->operationSuccess($operation);
        }
        catch(\Exception $e) {
            $this->dispatcher->fire(new Failed($extension, $e));

            return $handler->operationFailed(new Operation($e->getMessage()));
        }
    }

    /**
     * Imports the default settings to the
     *
     * @param OperationHandlerContract $handler
     * @param ExtensionContract $extension
     */
    private function importSettings(OperationHandlerContract $handler, ExtensionContract $extension) {
        try {
            $settings = $this->settingsFactory->createFromConfig($extension->getPath() . '/resources/config/settings.php');

            $extension->setSettings($settings);
            $handler->operationInfo(new Operation('Importing settings.'));
        }
        catch(\Exception $e) {
            // No need to throw an exception because of the settings file can be optional. In that case the required file will be not found.
        }
    }

}
