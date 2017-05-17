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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Support;

use InvalidArgumentException;
use Illuminate\Support\Manager as BaseManager;

abstract class Manager extends BaseManager
{

    /**
     * Define blacklisted character in name.
     *
     * @var array
     */
    protected $blacklisted = ['.'];

    /**
     * Create a new instance.
     *
     * @param  string  $driver
     *
     * @return object
     */
    public function make($driver = null)
    {
        return $this->driver($driver);
    }

    /**
     * Create a new driver instance.
     *
     * @param  string  $driverName
     *
     * @return object
     */
    protected function createDriver($driverName)
    {
        list($driver, $name) = $this->getDriverName($driverName);

        $method = 'create' . Str::studly($driver) . 'Driver';
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driverName);
        } elseif (method_exists($this, $method)) {
            return call_user_func([$this, $method], $name);
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  string  $driverName
     *
     * @return object
     */
    protected function callCustomCreator($driverName)
    {
        list($driver, $name) = $this->getDriverName($driverName);
        return call_user_func($this->customCreators[$driver], $this->app, $name);
    }

    /**
     * Get driver name.
     *
     * @param  string  $driverName
     *
     * @return array
     */
    protected function getDriverName($driverName)
    {
        if (false === strpos($driverName, '.')) {
            $driverName = "{$driverName}.default";
        }

        list($driver, $name) = explode('.', $driverName, 2);


        $this->checkNameIsNotBlacklisted($name);

        return [$driver, $name];
    }

    /**
     * Check if name is not blacklisted.
     *
     * @param  string  $name
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function checkNameIsNotBlacklisted($name)
    {
        if (Str::contains($name, $this->blacklisted)) {
            throw new InvalidArgumentException("Invalid character in driver name [{$name}].");
        }
    }

}
