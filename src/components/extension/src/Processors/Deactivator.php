<?php

declare(strict_types=1);

namespace Antares\Extension\Processors;

use Antares\Acl\Migration;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Events\Deactivated;
use Antares\Extension\Events\Deactivating;
use Antares\Extension\Events\Failed;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Model\Operation;
use Antares\Extension\Repositories\ComponentsRepository;
use Antares\Extension\Repositories\ExtensionsRepository;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Antares\Console\Kernel;
use Illuminate\Support\Facades\Log;

class Deactivator extends AbstractOperation {

    /**
     * @var ExtensionsRepository
     */
    protected $extensionsRepository;

    /**
     * @var Migration
     */
    protected $aclMigration;

    /**
     * Deactivator constructor.
     * @param Container $container
     * @param Dispatcher $dispatcher
     * @param Kernel $kernel
     * @param ExtensionsRepository $extensionsRepository
     * @param Migration $aclMigration
     * @param ComponentsRepository $componentsRepository
     */
    public function __construct(
        Container $container,
        Dispatcher $dispatcher,
        Kernel $kernel,
        ExtensionsRepository $extensionsRepository,
        Migration $aclMigration,
        ComponentsRepository $componentsRepository
    )
    {
        parent::__construct($container, $dispatcher, $kernel, $componentsRepository);

        $this->extensionsRepository = $extensionsRepository;
        $this->aclMigration         = $aclMigration;
    }

    /**
     * The run operation.
     *
     * @param OperationHandlerContract $handler
     * @param ExtensionContract $extension
     * @param array $flags (additional array with flags)
     * @return mixed
     * @throws \RuntimeException
     */
    public function run(OperationHandlerContract $handler, ExtensionContract $extension, array $flags = []) {
        try {
            $name = $extension->getPackage()->getName();

            if($this->componentsRepository->isRequired($name)) {
                throw new ExtensionException('The package [' . $name . '] cannot be deactivated due is required for whole system.');
            }

            $handler->operationInfo(new Operation('Deactivating the [' . $name . '] extension.'));
            $this->dispatcher->fire(new Deactivating($extension));
            $this->aclMigration->down($name);

            $this->extensionsRepository->save($extension, [
                'status' => ExtensionContract::STATUS_INSTALLED,
            ]);

            $this->dispatcher->fire(new Deactivated($extension));

            $operation = new Operation('The package [' . $name . '] has been successfully deactivated.');

            return $handler->operationSuccess($operation);
        }
        catch(\Exception $e) {
            Log::error($e);
            $this->dispatcher->fire(new Failed($extension, $e));

            return $handler->operationFailed(new Operation($e->getMessage()));
        }
    }

}
