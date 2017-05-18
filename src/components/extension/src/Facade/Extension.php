<?php

declare(strict_types=1);

namespace Antares\Extension\Facade;

use Antares\Extension\Manager;
use Illuminate\Support\Facades\Facade;

class Extension extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return Manager::class;
    }

}
