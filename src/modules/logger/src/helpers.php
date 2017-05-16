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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



if (!function_exists('error_log_menu')) {

    /**
     * Get the LogMenu instance.
     *
     * @return \Arcanedev\LogViewer\Contracts\LogMenuInterface
     */
    function error_log_menu()
    {
        return app('logger.log-viewer.menu');
    }

}

