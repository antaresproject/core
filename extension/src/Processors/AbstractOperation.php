<?php

declare(strict_types=1);

namespace Antares\Extension\Processors;

use Antares\Extension\Contracts\OperationContract;
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
     * AbstractOperation constructor.
     * @param Container $container
     * @param Dispatcher $dispatcher
     * @param Kernel $kernel
     */
    public function __construct(Container $container, Dispatcher $dispatcher, Kernel $kernel) {
        $this->container        = $container;
        $this->dispatcher       = $dispatcher;
        $this->kernel           = $kernel;
    }

}
