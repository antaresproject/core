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

interface DatabaseRestorator
{

    /**
     * create database temporary instance
     */
    public function create();

    /**
     * copy database instance from backup to primary database instance
     */
    public function copy();

    /**
     * create dump database between backup and primary database instance
     */
    public function dump();

    /**
     * drops temporary database instance
     */
    public function drop();
}
