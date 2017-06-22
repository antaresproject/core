<?php

declare(strict_types = 1);

namespace Antares\Extension\Processors;

use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Repositories\ComponentsRepository;
use Antares\Extension\Repositories\ExtensionsRepository;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Events\Activating;
use Antares\Extension\Events\Activated;
use Antares\Memory\Model\DeferedEvent;
use Antares\Extension\Model\Operation;
use Antares\Extension\Events\Failed;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Antares\Console\Kernel;
use Exception;
use Illuminate\Support\Facades\Log;

class Activator extends AbstractOperation
{

    /**
     * @var ExtensionsRepository
     */
    protected $extensionsRepository;

    /**
     * ACL processor.
     *
     * @var Acl
     */
    protected $aclMigration;

    /**
     * Activator constructor.
     * @param Container $container
     * @param Dispatcher $dispatcher
     * @param Kernel $kernel
     * @param ExtensionsRepository $extensionsRepository
     * @param Acl $aclMigration
     * @param ComponentsRepository $componentsRepository
     */
    public function __construct(Container $container, Dispatcher $dispatcher, Kernel $kernel, ExtensionsRepository $extensionsRepository, Acl $aclMigration, ComponentsRepository $componentsRepository)
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
     */
    public function run(OperationHandlerContract $handler, ExtensionContract $extension, array $flags = [])
    {

        try {
            $name = $extension->getPackage()->getName();

            $handler->operationInfo(new Operation('Activating the [' . $name . '] extension.'));
            $this->dispatcher->fire(new Activating($extension));
            $this->aclMigration->import($handler, $extension);

            $this->extensionsRepository->save($extension, [
                'status' => ExtensionContract::STATUS_ACTIVATED,
            ]);

            try {
                //DeferedEvent::query()->firstOrCreate(['name' => 'after.activated.' . $name]);
            } catch (Exception $ex) {
                
            }

            app(\Antares\Installation\Listeners\IncrementProgress::class)->advanceProgress();

            $operation = new Operation('The package [' . $name . '] has been successfully activated.');

            return $handler->operationSuccess($operation);
        } catch (Exception $e) {
            Log::error($e);
            $this->dispatcher->fire(new Failed($extension, $e));

            return $handler->operationFailed(new Operation($e->getMessage()));
        }
    }

}
