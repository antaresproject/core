<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Contracts;

interface Resolver
{

    /**
     * decompress migration file with files mapping
     */
    public function resolve();

    /**
     * set path of compressed migration file
     * 
     * @param String $path
     */
    public function setPath($path);

    /**
     * update version setter
     * 
     * @param String $version
     * @return \Antares\Updater\Filesystem\Resolver
     */
    public function setVersion($version);
}
