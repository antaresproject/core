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

interface ModuleListener
{

    /**
     * updating module
     * 
     * @param String $name
     * @param String $version
     */
    public function update($name, $version);

    /**
     * module update success
     * 
     * @param mixed $data
     * @return \Illuminate\View\View
     */
    public function success();

    /**
     * module update failed
     * 
     * @param mixed $data
     * @return \Illuminate\View\View
     */
    public function failed($data);
}
