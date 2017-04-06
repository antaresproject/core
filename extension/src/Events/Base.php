<?php

declare(strict_types=1);

namespace Antares\Extension\Events;

use Antares\Contracts\Events\EventContract;
use Antares\Extension\Contracts\ExtensionContract;

abstract class Base implements EventContract {

    /**
     * @var ExtensionContract
     */
	public $extensionContract;

    /**
     * Base constructor.
     * @param ExtensionContract $extensionContract
     */
	public function __construct(ExtensionContract $extensionContract) {
		$this->extensionContract = $extensionContract;
	}

}
