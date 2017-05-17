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


namespace Antares\Messages;

use Antares\Support\Providers\MiddlewareServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;

class MessagesServiceProvider extends MiddlewareServiceProvider
{

    /**
     * The application's middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        Http\Middleware\StoreMessageBag::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('antares.messages', function ($app) {
            return (new MessageBag())->setSessionStore($app->make('session.store'));
        });
    }

    public function boot(Router $router, Kernel $kernel)
    {
        parent::boot($router, $kernel);
        $path = realpath(__DIR__ . '/../');
        $this->addConfigComponent('antares/messages', 'antares/messages', "{$path}/resources/config");
    }

}
