<?php

declare(strict_types=1);

namespace Antares\Extension\Processors;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Events\Activated;
use Antares\Extension\Events\Activating;
use Antares\Extension\Events\Failed;
use Antares\Extension\Model\Operation;
use Antares\Extension\Repositories\ExtensionsRepository;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Antares\Console\Kernel;

class Activator extends AbstractOperation {

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
     */
    public function __construct(
        Container $container,
        Dispatcher $dispatcher,
        Kernel $kernel,
        ExtensionsRepository $extensionsRepository,
        Acl $aclMigration
    )
    {
        parent::__construct($container, $dispatcher, $kernel);

        $this->extensionsRepository = $extensionsRepository;
        $this->aclMigration          = $aclMigration;
    }

    /**
     * The run operation.
     *
     * @param OperationHandlerContract $handler
     * @param ExtensionContract $extension
     * @param array $flags (additional array with flags)
     * @return mixed
     */
    public function run(OperationHandlerContract $handler, ExtensionContract $extension, array $flags = []) {
        try {
            $name = $extension->getPackage()->getName();

            $handler->operationInfo(new Operation('Activating the [' . $name . '] extension.'));
            $this->dispatcher->fire(new Activating($extension));
            $this->aclMigration->import($handler, $extension);

            $this->extensionsRepository->save($extension, [
                'status' => ExtensionContract::STATUS_ACTIVATED,
            ]);

            $this->dispatcher->fire(new Activated($extension));

            $operation = new Operation('The package [' . $name . '] has been successfully activated.');

            return $handler->operationSuccess($operation);
        }
        catch(\Exception $e) {
            $this->dispatcher->fire(new Failed($extension, $e));

            return $handler->operationFailed(new Operation($e->getMessage()));
        }
    }

}
