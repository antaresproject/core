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

namespace Antares\Foundation\Support\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Antares\Foundation\Support\Providers\Traits\RouteProviderTrait;
use Antares\Support\Providers\Traits\MiddlewareProviderTrait;
use Antares\Support\Providers\Traits\PackageProviderTrait;
use Antares\Support\Providers\Traits\EventProviderTrait;
use Antares\Support\Providers\Traits\BindableTrait;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Http\Kernel;
use Antares\Api\Http\Router\Adapter;
use Antares\Foundation\MenuComposer;
use Illuminate\Routing\Router;
use RuntimeException;
use ReflectionClass;
use SplFileInfo;

abstract class ModuleServiceProvider extends ServiceProvider
{

    use EventProviderTrait,
        MiddlewareProviderTrait,
        PackageProviderTrait,
        RouteProviderTrait,
        BindableTrait;

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace;

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'app';

    /**
     * The fallback route prefix.
     *
     * @var string
     */
    protected $routePrefix = '/';

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * The application's or extension's middleware stack.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * The application's or extension's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    /**
     * extension path
     *
     * @var String 
     */
    protected $extensionPath = '';

    /**
     * Enable module routes for API requests if the API component is enabled.
     *
     * @var bool
     */
    protected $supportApi = true;

    /**
     * (non-PHPdoc)
     * @see \Illuminate\Foundation\Support\Providers\RouteServiceProvider::register()
     */
    public function register()
    {
        parent::register();
        $this->bind();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Illuminate\Foundation\Support\Providers\RouteServiceProvider::boot()
     */
    public function boot()
    {
        $router = $this->app->make(Router::class);
        $events = $this->app->make(Dispatcher::class);
        $kernel = $this->app->make(Kernel::class);

        $this->setExtensionPath();

        $this->registerEventListeners($events);
        $this->registerRouteMiddleware($router, $kernel);
        $this->bootConfiguration();
        $this->bootMemory();
        $this->bootExtensionComponents();
        $this->bootExtensionRouting();
        $this->bootApiRouting($router);
    }

    /**
     * Register router for API requests.
     *
     * @param Router $router
     */
    private function bootApiRouting(Router $router)
    {
        if (!$this->app->make('antares.request')->shouldMakeApiResponse()) {
            return;
        }

        $routerAdapter = $this->app->make(Adapter::class);
        $routes        = [];

        foreach ($router->getRoutes()->getRoutes() as $route) {
            $routeActionName = $route->getActionName();

            if (starts_with($routeActionName, $this->namespace)) {
                $routes[] = $route;
            }
        }

        $routerAdapter->adaptRoutes($routes, $this->namespace);
    }

    /**
     * extension path resolver
     * 
     * @return String
     */
    protected function setExtensionPath()
    {
        $reflection          = new ReflectionClass($this);
        $filename            = $reflection->getFileName();
        $splFileInfo         = new SplFileInfo($filename);
        return $this->extensionPath = $splFileInfo->getPath();
    }

    /**
     * Boot extension routing.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $path = $this->extensionPath;

        $routes = [
            'backend'  => [
                $path . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'backend.php',
                $path . DIRECTORY_SEPARATOR . 'routes.php'
            ],
            'frontend' => [
                $path . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'frontend.php'
            ]
        ];


        foreach ($routes as $keyname => $routePaths) {
            foreach ($routePaths as $route) {
                if (!file_exists($route)) {
                    continue;
                }
                switch ($keyname) {
                    case 'backend':
                        $this->loadBackendRoutesFrom($route);
                        break;
                    case 'frontend':
                        $this->loadFrontendRoutesFrom($route);
                        break;
                }
            }
        }
    }

    /**
     * boots extension memory
     * 
     * @throws RuntimeException
     */
    protected function bootMemory()
    {
        if (!isset($this->routeGroup) or strlen($this->routeGroup) <= 0) {
            throw new RuntimeException('Invalid extension route group.');
        }

        $this->app->make('antares.acl')->make($this->routeGroup)->attach(
                $this->app->make('antares.platform.memory')
        );
    }

    /**
     * boot extension configuration
     */
    protected function bootConfiguration()
    {
        $path = $this->extensionPath . '/../';
        $this->addConfigComponent($this->routeGroup, $this->routeGroup, "{$path}/resources/config");
        $this->addLanguageComponent($this->routeGroup, $this->routeGroup, "{$path}/resources/lang");
        $this->addViewComponent($this->routeGroup, $this->routeGroup, "{$path}/resources/views");
    }

    /**
     * Boot extension components.
     *
     * @return void
     */
    protected function bootExtensionComponents()
    {
        
    }

    /**
     * booting extension routing
     */
    protected function bootExtensionRouting()
    {
        if (!$this->app->routesAreCached()) {

            $this->afterExtensionLoaded(function () {

                $this->loadRoutes();
            });
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Illuminate\Foundation\Support\Providers\RouteServiceProvider::loadCachedRoutes()
     */
    protected function loadCachedRoutes()
    {
        throw new RuntimeException('loadCachedRoutes() method is not supported.');
    }

    /**
     * (non-PHPdoc)
     * @see \Illuminate\Foundation\Support\Providers\RouteServiceProvider::setRootControllerNamespace()
     */
    protected function setRootControllerNamespace()
    {
        throw new RuntimeException('setRootControllerNamespace() method is not supported.');
    }

    /**
     * binds contracts to objects
     * 
     * @return boolean
     */
    protected function bind()
    {
        if (!isset($this->di)) {
            return false;
        }
        foreach ($this->di as $contract => $object) {
            $this->app->bind($contract, $object);
        }
    }

    /**
     * attaches menu to application container
     * 
     * @param mixed $classnames
     * @return mixed
     */
    protected function attachMenu($classnames)
    {
        $menuComposer = MenuComposer::getInstance();
        if (is_array($classnames)) {
            foreach ($classnames as $classname) {
                $menuComposer->compose($classname);
            }
            return;
        }
        return $menuComposer->compose($classnames);
    }

    /**
     * bind contracts to its classes
     * 
     * @return boolean
     */
    protected function bindContracts()
    {
        $dir        = dirname((new ReflectionClass(get_called_class()))->getFileName());
        $configPath = $dir . '/../resources/config/config.php';
        if (!file_exists($configPath)) {
            return false;
        }
        if (empty($di = require $configPath)) {
            return false;
        }

        foreach ($di['di'] as $contract => $class) {
            $this->app->bind($contract, $class);
        }
    }

}
