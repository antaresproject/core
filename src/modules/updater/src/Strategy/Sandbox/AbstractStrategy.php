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






namespace Antares\Updater\Strategy\Sandbox;

use Antares\Updater\Traits\Note;
use Illuminate\Support\Facades\Input;
use Exception;

abstract class AbstractStrategy
{

    use Note;

    /**
     * update version
     * 
     * @var String
     */
    protected static $version;

    /**
     * build path
     *
     * @var String 
     */
    protected static $buildPath;

    /**
     * public build path
     *
     * @var String 
     */
    protected static $publicBuildPath;

    /**
     * database name
     *
     * @var String 
     */
    protected static $databaseName;

    /**
     * update version getter
     * 
     * @return type
     * @throws Exception
     */
    public function getVersion()
    {
        if (!is_null(self::$version)) {
            return self::$version;
        }
        $version = Input::get('version');
        if (is_null($version)) {
            throw new Exception('Invalid update version provided.');
        }
        if (is_null(self::$version)) {
            self::$version = $this->format($version);
        }
        return self::$version;
    }

    /**
     * format version number
     * 
     * @param String $version
     */
    private function format($version)
    {
        return str_replace(['.', ','], '_', $version);
    }

    /**
     * build path getter
     * 
     * @return String
     * @throws Exception
     */
    public function getBuildPath()
    {

        $buildPath = config('antares/updater::sandbox.files.build_path');
        if (is_null($buildPath)) {
            throw new Exception('Build path is not set.');
        }
        if (is_null(self::$buildPath)) {
            self::$buildPath = $this->formatPath($buildPath);
        }
        return self::$buildPath;
    }

    /**
     * public build path getter
     * 
     * @return String
     * @throws Exception
     */
    public function getPublicPath()
    {
        $publicBuildPath = config('antares/updater::sandbox.files.public_build_path');
        if (is_null($publicBuildPath)) {
            throw new Exception('Public build path is not set.');
        }
        if (is_null(self::$publicBuildPath)) {
            self::$publicBuildPath = $this->formatPath($publicBuildPath);
        }
        return self::$publicBuildPath;
    }

    /**
     * database name getter
     * 
     * @return String
     * @throws Exception
     */
    protected function databaseName()
    {
        $prefix = config('antares/updater::sandbox.database.prefix');
        if (is_null($prefix)) {
            throw new Exception('Database prefix is not set.');
        }
        if (is_null(self::$databaseName)) {
            $version            = $this->getVersion();
            self::$databaseName = $prefix . $this->format($version);
        }
        return self::$databaseName;
    }

    /**
     * path formatter
     * 
     * @param String $path
     * @return String
     */
    private function formatPath($path)
    {
        $version = $this->getVersion();
        return $path . DIRECTORY_SEPARATOR . 'build_' . $this->format($version);
    }

    /**
     * version setter
     * 
     * @param String $version
     * @return \Antares\Updater\Strategy\Sandbox\AbstractStrategy
     */
    public function setVersion($version = null)
    {
        if (!is_null($version)) {
            self::$version = $version;
        }
        return $this;
    }

}
