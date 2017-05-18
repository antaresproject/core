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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents;

use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\UI\UIComponents\Http\Handlers\ComponentsSelector;
use Antares\Support\Providers\Traits\AliasesProviderTrait;
use Antares\UI\UIComponents\Http\Middleware\Middleware;
use Antares\UI\UIComponents\Registry\TemplatesRegistry;
use Illuminate\Cache\Repository as CacheRepository;
use Antares\UI\UIComponents\Repository\Repository;
use Antares\UI\UIComponents\Model\ComponentParams;
use Antares\UI\UIComponents\Observer\Observer;
use Antares\UI\UIComponents\Service\Service;
use Illuminate\Routing\Router;
use Antares\Registry\Registry;

ini_set('display_errors', '1');

class UiComponentsServiceProvider extends ModuleServiceProvider
{

    use AliasesProviderTrait;

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\UI\UIComponents\Http\Controllers\Admin';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/ui-components';

    /**
     * List of widgets aliases.
     *
     * @var array
     */
    protected $aliases = [
        'antares.widgets' => 'Antares\UI\UIComponents\Factory',
    ];

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['antares.ui-components.installed'] = true;
        $this->app->bind('Antares\UI\UIComponents\Contracts\AfterValidate', 'Antares\UI\UIComponents\Adapter\AfterValidateAdapter');
    }

    /**
     * Boot extension routing.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $path = realpath(__DIR__);
        $this->loadBackendRoutesFrom("{$path}/routes.php");
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function bootExtensionComponents()
    {
        if (!app('antares.installed')) {
            return;
        }

        $router = app(Router::class);
        $router->pushMiddlewareToGroup('web', Middleware::class);

        $path = realpath(__DIR__ . '/../');

        $this->addConfigComponent('antares/ui-components', 'antares/ui-components', "{$path}/resources/config");
        $this->addLanguageComponent('antares/ui-components', 'antares/ui-components', "{$path}/resources/lang");
        $this->addViewComponent('antares/ui-components', 'antares/ui-components', "{$path}/resources/views");
        $this->bootMemory();
        $this->pushTemplates();

        $this->app->bind('ui-components', function () {
            return new Repository($this->app, app(CacheRepository::class));
        });

        $this->app->bind('ui-components-templates', function () {
            return TemplatesRegistry::get('templates');
        });


        ComponentParams::observe(new Observer());


        $this->app->singleton(Service::class, function () {
            return new Service($this->app->make(Repository::class));
        });

        if (!$this->app->runningInConsole()) {
            $this->bootMenus();
        }
    }

    /**
     * Booting top left menu
     */
    public function bootMenus()
    {
        $name = 'menu.top.right';
        view()->composer('antares/foundation::*', function () use ($name) {
            if (!Registry::isRegistered('menu.' . $name)) {
                $this->app->instance($name, $this->app->make('antares.widget')->make($name));
                $selector = new ComponentsSelector($this->app, $name);
                $selector->handle();
                Registry::set('menu.' . $name, $selector);
            }
        });
    }

    /**
     * push templates into registry
     */
    protected function pushTemplates()
    {
        $templateFinder = new TemplateFinder($this->app);
        TemplatesRegistry::set('templates', $templateFinder->detect());
    }

    /**
     * booting events
     */
    protected function bootMemory()
    {
        $this->app->make('antares.acl')->make('antares/ui-components')->attach($this->app->make('antares.platform.memory'));
    }

}
