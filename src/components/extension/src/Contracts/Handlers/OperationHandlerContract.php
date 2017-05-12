<?php

declare(strict_types=1);

namespace Antares\Extension\Contracts\Handlers;

use Antares\Extension\Model\Operation;

interface OperationHandlerContract
{

    /**
     * @param Operation $operation
     * @return mixed
     */
    public function operationSuccess(Operation $operation);

    /**
     * @param Operation $operation
     * @return mixed
     */
    public function operationFailed(Operation $operation);

	/**
	 * @param Operation $operation
	 * @return mixed
	 */
	public function operationInfo(Operation $operation);

}
