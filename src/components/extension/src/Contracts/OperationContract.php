<?php

declare(strict_types=1);

namespace Antares\Extension\Contracts;

use Antares\Extension\Contracts\Handlers\OperationHandlerContract;

interface OperationContract {

    /**
     * The run operation.
     *
	 * @param OperationHandlerContract $handler
     * @param ExtensionContract $extension
     * @param array $flags (additional array with flags)
     * @return mixed
     */
    public function run(OperationHandlerContract $handler, ExtensionContract $extension, array $flags = []);

}
