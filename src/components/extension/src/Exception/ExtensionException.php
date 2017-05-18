<?php

declare(strict_types=1);

namespace Antares\Extension\Exception;

use Antares\Extension\Model\Operation;
use Exception as BaseException;

class ExtensionException extends BaseException
{

    /**
     * @return Operation
     */
    public function getOperationModel() : Operation {
        return new Operation($this->getMessage());
    }

}
