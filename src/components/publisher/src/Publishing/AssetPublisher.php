<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Publisher\Publishing;

use InvalidArgumentException;

class AssetPublisher extends Publisher
{

    /**
     * Get the source assets directory to publish.
     *
     * @param  string  $package
     * @param  string  $packagePath
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function getSource($package, $packagePath)
    {
        $sources = [
            "{$packagePath}/{$package}/resources/public",
            "{$packagePath}/{$package}/public",
        ];

        foreach ($sources as $source) {
            $_source = str_replace('/', DIRECTORY_SEPARATOR, $source);

            if ($this->files->isDirectory($_source)) {
                return $_source;
            }
        }

        throw new InvalidArgumentException('Assets not found.');
    }

    /**
     * deletes asset 
     * @param String $path
     * @return boolean
     */
    public function delete($path)
    {
        return $this->files->deleteDirectory($path);
    }

}
