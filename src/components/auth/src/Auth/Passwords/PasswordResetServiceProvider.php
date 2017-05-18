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

namespace Antares\Auth\Passwords;

use Illuminate\Contracts\Foundation\Application;

class PasswordResetServiceProvider extends \Illuminate\Auth\Passwords\PasswordResetServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the password broker instance.
     *
     * @return void
     */
    protected function registerPasswordBroker()
    {
        $this->app->singleton('auth.password', function (Application $app) {
            return new PasswordBrokerManager($app);
        });
        $this->app->bind('auth.password.broker', function (Application $app) {
            return $app->make('auth.password')->broker();
        });

//        $this->app->singleton('auth.password.broker', function ($app) {
//            $tokens   = $app->make('auth.password.tokens');
//            $users    = $app->make('auth')->driver()->getProvider();
//            $notifier = $app->make('antares.notifier')->driver();
//            $view     = $app->make('config')->get('auth.password.email');
//            return new PasswordBroker($tokens, $users, $notifier, $view);
//        });
    }

}
