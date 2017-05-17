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
 namespace Antares\Contracts\Extension;

interface StatusChecker
{
    /**
     * Check current mode is equal given $mode.
     *
     * @param  string  $mode
     *
     * @return bool
     */
    public function is($mode);

    /**
     * Check current mode is not equal given $mode.
     *
     * @param  string  $mode
     *
     * @return bool
     */
    public function isNot($mode);

    /**
     * Get current mode.
     *
     * @return string
     */
    public function mode();
}
