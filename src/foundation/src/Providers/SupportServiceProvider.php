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


namespace Antares\Foundation\Providers;

use Antares\Model\Role;
use Antares\Model\User;
use Antares\Model\Action;
use Antares\Model\UserRole;
use Antares\Model\Component;
use Antares\Model\Permission;
use Illuminate\Support\ServiceProvider;
use Antares\Foundation\Publisher\PublisherManager;

class SupportServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPublisher();

        $this->registerRoleEloquent();

        $this->registerComponentEloquent();

        $this->registerActionComponentEloquent();

        $this->registerUserRoleEloquent();

        $this->registerComponentPermissionEloquent();

        $this->registerUserEloquent();
    }

    /**
     * Register the service provider for publisher.
     *
     * @return void
     */
    protected function registerPublisher()
    {
        $this->app->singleton('antares.publisher', function ($app) {
            $memory = $app->make('antares.platform.memory');

            return (new PublisherManager($app))->attach($memory);
        });
    }

    /**
     * Register the service provider for component.
     *
     * @return void
     */
    protected function registerComponentEloquent()
    {
        $this->app->bind('antares.component', function () {
            return new Component();
        });
    }

    /**
     * Register the service provider for component.
     *
     * @return void
     */
    protected function registerActionComponentEloquent()
    {
        $this->app->bind('antares.component.action', function () {
            return new Action();
        });
    }

    /**
     * Register the service provider for roles.
     *
     * @return void
     */
    protected function registerRoleEloquent()
    {
        $this->app->bind('antares.role', function () {
            return new Role();
        });
    }

    /**
     * Register the service provider for component permission.
     *
     * @return void
     */
    public function registerComponentPermissionEloquent()
    {
        $this->app->bind('antares.component.permission', function () {
            return new Permission();
        });
    }

    /**
     * Register the service provider for user.
     *
     * @return void
     */
    protected function registerUserEloquent()
    {
        $this->app->bind('antares.user', function () {
            return new User();
        });
    }

    /**
     * Register the service provider for user roles.
     *
     * @return void
     */
    protected function registerUserRoleEloquent()
    {
        $this->app->bind('antares.user.role', function () {
            return new UserRole();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'antares.publisher', 'antares.role', 'antares.user',
        ];
    }

}
