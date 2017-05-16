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

interface UpdateListener
{

    /**
     * installation is starting
     * 
     * @param String $version
     * @return \Illuminate\View\View
     */
    public function start($version);

    /**
     * when updating is successfull
     */
    public function success($data);

    /**
     * when updating is failed
     */
    public function failed($message);
}
