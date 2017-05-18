<?php

declare(strict_types=1);

namespace Antares\Extension\Events;

use Antares\Extension\Contracts\ExtensionContract;
use Exception;

class Failed extends Base {

    /**
     * @var Exception
     */
    public $exception;

    /**
     * Failed constructor.
     * @param ExtensionContract $extensionContract
     * @param Exception $exception
     */
    public function __construct(ExtensionContract $extensionContract, Exception $exception) {
        parent::__construct($extensionContract);

        $this->exception = $exception;
    }

}
