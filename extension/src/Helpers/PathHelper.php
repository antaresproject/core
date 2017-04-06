<?php

declare(strict_types=1);

namespace Antares\Extension\Helpers;

class PathHelper {

    /**
     * Returns a normalized path.
     *
     * @param string $path
     * @return string
     */
    public static function getNormalizedPath(string $path) : string {
        return trim(str_replace('-', '_', $path));
    }

}
