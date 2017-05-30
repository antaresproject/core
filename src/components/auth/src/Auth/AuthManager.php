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


namespace Antares\Auth;

use Illuminate\Auth\AuthManager as BaseManager;

class AuthManager extends BaseManager
{

    /**
     * Create a session based authentication guard.
     *
     * @param  string  $name
     * @param  array  $config
     * @return \Illuminate\Auth\SessionGuard
     */
    public function createSessionDriver($name, $config)
    {
        $provider = $this->createUserProvider($config['provider']);
        $guard    = new SessionGuard($name, $provider, $this->app->make('session.store'));
        if (method_exists($guard, 'setCookieJar')) {
            $guard->setCookieJar($this->app->make('cookie'));
        }

        if (method_exists($guard, 'setDispatcher')) {
            $guard->setDispatcher($this->app->make('events'));
        }

        if (method_exists($guard, 'setRequest')) {
            $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
        }

        return $guard;
    }

    public function setGuard($guard)
    {
        $this->guards['multi'] = $guard;
    }

}
