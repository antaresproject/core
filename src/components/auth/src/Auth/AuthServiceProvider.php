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

use Antares\Contracts\Authorization\Factory as FactoryContract;
use Illuminate\Auth\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Antares\Authorization\Policy;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerPolicyAfterResolvingHandler();
    }

    /**
     * Register the service provider for Auth.
     *
     * @return void
     */
    protected function registerAuthenticator()
    {
        $this->app->singleton('auth', function (Application $app) {
            // Once the authentication service has actually been requested by the developer
            // we will set a variable in the application indicating such. This helps us
            // know that we need to set any queued cookies in the after event later.
            $app['auth.loaded'] = true;

            return new AuthManager($app);
        });
        $this->app->singleton('auth.driver', function (Application $app) {
            return $app->make('auth')->guard();
        });
        $this->app->singleton('multiuser', function (Application $app) {
            return new Multiuser();
        });
    }

    /**
     * Register the Policy after resolving handler.
     *
     * @return void
     */
    protected function registerPolicyAfterResolvingHandler()
    {
        $this->app->afterResolving(Policy::class, function (Policy $policy) {
            return $policy->setAuthorization($this->app->make(FactoryContract::class));
        });
    }

    /**
     * Boot service provider
     */
    public function boot()
    {
        Event::listen('antares.auth', 'Antares\Auth\Composers\MultiuserPlaceholder@onBootExtension');
    }

}
