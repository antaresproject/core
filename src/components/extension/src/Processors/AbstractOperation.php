<?php

declare(strict_types=1);

namespace Antares\Extension\Processors;

use Antares\Extension\Contracts\OperationContract;
use Antares\Extension\Repositories\ComponentsRepository;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Antares\Console\Kernel;

abstract class AbstractOperation implements OperationContract {

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var ComponentsRepository
     */
    protected $componentsRepository;

    /**
     * AbstractOperation constructor.
     * @param Container $container
     * @param Dispatcher $dispatcher
     * @param Kernel $kernel
     * @param ComponentsRepository $componentsRepository
     */
    public function __construct(Container $container, Dispatcher $dispatcher, Kernel $kernel, ComponentsRepository $componentsRepository) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
        $this->kernel = $kernel;
        $this->componentsRepository = $componentsRepository;
    }

}
