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

interface Requirement
{
    /**
     * Check all requirement.
     *
     * @return bool
     */
    public function check();

    /**
     * Get checklist result.
     *
     * @return array
     */
    public function getChecklist();

    /**
     * Get installable status.
     *
     * @return bool
     */
    public function isInstallable();
}
