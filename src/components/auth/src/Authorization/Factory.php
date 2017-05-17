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


namespace Antares\Authorization;

use Illuminate\Support\Arr;
use Antares\Contracts\Auth\Guard;
use Antares\Contracts\Memory\Provider;
use Antares\Contracts\Authorization\Factory as FactoryContract;

class Factory implements FactoryContract
{

    /**
     * Auth instance.
     *
     * @var \Antares\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Cache ACL instance so we can reuse it on multiple request.
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * Construct a new Environment.
     *
     * @param  \Antares\Contracts\Auth\Guard  $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Initiate a new ACL Container instance.
     *
     * @param  string  $name
     * @param  \Antares\Contracts\Memory\Provider  $memory
     *
     * @return \Antares\Contracts\Authorization\Authorization
     */
    public function make($name = null, Provider $memory = null)
    {
        if ($name === null) {
            $name = 'default';
        }

        if (!isset($this->drivers[$name])) {
            $this->drivers[$name] = new Authorization($this->auth, $name, $memory);
        }

        return $this->drivers[$name];
    }

    /**
     * Register an ACL Container instance with Closure.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     *
     * @return \Antares\Contracts\Authorization\Authorization
     */
    public function register($name, $callback = null)
    {
        if (is_callable($name)) {
            $callback = $name;
            $name     = null;
        }

        $instance = $this->make($name);

        call_user_func($callback, $instance);

        return $instance;
    }

    /**
     * Manipulate and synchronize roles.
     *
     * @param  string  $method
     * @param  array   $parameters
     *
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        $response = [];

        foreach ($this->drivers as $acl) {
            $response[] = call_user_func_array([$acl, $method], $parameters);
        }

        return $response;
    }

    /**
     * Shutdown/finish all ACL.
     *
     * @return $this
     */
    public function finish()
    {
        foreach ($this->drivers as $acl) {
            $acl->sync();
        }

        $this->drivers = [];

        return $this;
    }

    /**
     * Get all ACL instances.
     *
     * @return array
     */
    public function all()
    {
        return $this->drivers;
    }

    /**
     * Get ACL instance by name.
     *
     * @param  string  $name
     *
     * @return \Antares\Contracts\Authorization\Authorization
     */
    public function get($name)
    {
        return Arr::get($this->drivers, $name);
    }

}
