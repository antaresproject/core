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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Contracts\Installation;

interface Installation
{

    /**
     * Boot installer files.
     *
     * @return void
     */
    public function bootInstallerFiles();

    /**
     * Migrate Antares schema.
     *
     * @return bool
     */
    public function migrate();

    /**
     * Create administrator account.
     *
     * @param  array  $input
     * @param  bool   $allowMultiple
     *
     * @return bool
     */
    public function createAdmin($input, $allowMultiple = true);

    /**
     * Sets queue for installation of components.
     *
     * @return void
     */
    public function runComponentsInstallation();

}
