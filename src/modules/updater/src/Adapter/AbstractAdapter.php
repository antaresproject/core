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






namespace Antares\Updater\Adapter;

use Illuminate\Contracts\Foundation\Application;
use Antares\Updater\Model\Version;
use Antares\Updater\Contracts\Adapter;
use Antares\Support\Facades\Foundation;

abstract class AbstractAdapter implements Adapter
{

    /**
     * application instance
     *
     * @var Application
     */
    protected $app;

    /**
     * version details container
     *
     * @var array 
     */
    protected $version;

    /**
     * configuration container
     *
     * @var array 
     */
    protected $config;

    /**
     * data container with version details
     *
     * @var mixed
     */
    protected $data;

    /**
     * information about actual version
     *
     * @var String 
     */
    private static $actualVersion;

    /**
     * information about actual version
     *
     * @var String 
     */
    private static $previousVersion;

    /**
     * constructing
     * 
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app    = $app;
        $this->config = $app->make('config')->get('antares/updater::service.adapters.default');
        if (is_null(self::$actualVersion)) {
            self::$actualVersion = Version::actual()->first()->app_version;
        }
    }

    /**
     * retrive system version
     */
    abstract public function retrive();

    /**
     * does the version of system is newer than installed
     * 
     * @return boolean
     */
    public function isNewer()
    {
        $available = $this->retrive();
        return array_get($available, 'version') > self::$actualVersion;
    }

    /**
     * gets information about change log in new version
     */
    public function getChangeLog()
    {
        return array_get($this->data, 'changelog', null);
    }

    /**
     * gets description about new version
     */
    public function getDescription()
    {
        return array_get($this->data, 'description', null);
    }

    /**
     * gets version
     */
    public function getVersion()
    {
        return array_get($this->data, 'version', self::$actualVersion);
    }

    /**
     * get path of update script
     * 
     * @return String
     */
    public function getPath()
    {
        return array_get($this->data, 'path', '');
    }

    /**
     * get information about actual version
     * 
     * @return String
     */
    public function getActualVersion()
    {
        return self::$actualVersion;
    }

    /**
     * getting new version of sandbox instance
     * 
     * @return String
     */
    public function getNextVersion()
    {
        $actual = $this->getActualVersion();
        $model  = Foundation::make('Antares\Updater\Model\Sandbox')->where('version', $actual)->first();
        return (is_null($model)) ? $actual : $actual . str_random(5);
    }

    /**
     * previous version getter
     * 
     * @return String
     */
    public function getPreviousVersion()
    {
        if (is_null(self::$previousVersion)) {
            $previous              = Version::previous()->first();
            self::$previousVersion = (is_null($previous)) ? self::$actualVersion : $previous->app_version;
        }
        return self::$previousVersion;
    }

    /**
     * refresh application version info
     * 
     * @return \Antares\Updater\Adapter\AbstractAdapter
     */
    public function refresh()
    {
        $this->retrive();
        return $this;
    }

}
