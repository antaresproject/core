<?php

declare(strict_types=1);

namespace Antares\Extension\Model;

use Antares\Extension\Contracts\ExtensionContract;

class Types {

    const TYPE_CORE = 'Core';

    const TYPE_ADDITIONAL = 'Additional';

    /**
     * Returns list of types.
     *
     * @return array
     */
    public static function all() : array {
        return [self::TYPE_CORE, self::TYPE_ADDITIONAL];
    }

    /**
     * Returns a type by the given extension.
     *
     * @param ExtensionContract $extension
     * @return string
     */
    public static function getTypeByExtension(ExtensionContract $extension) : string {
        if($extension->isRequired()) {
            return self::TYPE_CORE;
        }

        return self::TYPE_ADDITIONAL;
    }

}
