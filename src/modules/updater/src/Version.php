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






namespace Antares\Updater;

use Antares\Updater\Adapter\JsonAdapter;

class Version
{

    /**
     * @todo add external checking
     * 
     * @return string the version of Antares framework
     */
    public static function getAppVersion()
    {
        return '0.9';
    }

    /**
     * @todo add external checking
     * 
     * @return string the db version of Antares framework
     */
    public static function getDbVersion()
    {
        return '0.9';
    }

    /**
     * Gets adapter instance
     * 
     * @return Adapter\AbstractAdapter
     */
    public static function getAdapter()
    {
        return app(JsonAdapter::class);
    }

}
