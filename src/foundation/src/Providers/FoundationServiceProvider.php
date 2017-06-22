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

use Antares\UI\UIComponents\Http\Middleware\Middleware as UIComponentsMiddleware;
use Antares\Foundation\Listeners\AfterExtensionOperation;
use Antares\Support\Providers\Traits\AliasesProviderTrait;
use Antares\Support\Providers\ServiceProvider;
use Antares\UI\UIComponents\TemplateFinder;
use Antares\Html\Middleware\FormMiddleware;
use Monolog\Handler\RotatingFileHandler;
use Illuminate\Filesystem\Filesystem;
use Antares\Foundation\Notification;
use Illuminate\Support\Facades\Log;
use Antares\Foundation\Foundation;
use Antares\UI\UIComponents\Factory;
use Antares\UI\UIComponents\Finder;
use Antares\Foundation\Request;
use Illuminate\Routing\Router;
use Antares\Foundation\Areas;
use Illuminate\Http\Response;
use Antares\Foundation\Meta;
use Exception;

class FoundationServiceProvider extends ServiceProvider
{

    use AliasesProviderTrait;

    /**
     * List of core aliases.
     *
     * @var array
     */
    protected $aliases = [
        'app'                     => 'Antares\Foundation\Application',
        'config'                  => 'Antares\Config\Repository',
        'auth.driver'             => ['Antares\Auth\Guard', 'Antares\Contracts\Auth\Guard'],
        'antares.platform.acl'    => ['Antares\Authorization\Authorization', 'Antares\Contracts\Authorization\Authorization'],
        'antares.platform.memory' => ['Antares\Memory\Provider', 'Antares\Contracts\Memory\Provider'],
        'antares.acl'             => ['Antares\Authorization\Factory', 'Antares\Contracts\Authorization\Factory'],
        'antares.app'             => ['Antares\Foundation\Foundation', 'Antares\Contracts\Foundation\Foundation'],
        'antares.asset'           => 'Antares\Asset\Factory',
        'antares.decorator'       => 'Antares\View\Decorator',
        'antares.form'            => ['Antares\Html\Form\Factory', 'Antares\Contracts\Html\Form\Factory'],
        'antares.mail'            => 'Antares\Notifier\Mailer',
        'antares.memory'          => 'Antares\Memory\MemoryManager',
        'antares.messages'        => ['Antares\Messages\MessageBag', 'Antares\Contracts\Messages\MessageBag'],
        'antares.notifier'        => 'Antares\Notifier\NotifierManager',
        'antares.publisher'       => 'Antares\Foundation\Publisher\PublisherManager',
        'antares.resources'       => 'Antares\Resources\Factory',
        'antares.meta'            => 'Antares\Foundation\Meta',
        'antares.theme'           => 'Antares\View\Theme\ThemeManager',
        'antares.widget'          => 'Antares\UI\WidgetManager'
    ];

    /**
     * List of core facades.
     *
     * @var array
     */
    protected $facades = [];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->registerFoundation();

        $this->registerMeta();

        $this->registerFacadesAliases();

        $this->registerCoreContainerAliases();

        $this->registerEvents();

        $this->registerWidgetsFinder();

        $this->registerWidgetsTemplateFinder();

        $this->registerRequestResolver();

        $this->app->singleton('antares.ui-components', function ($app) {
            return new Factory($app, new Response());
        });
        $this->app->singleton('antares.areas', function ($app) {
            return $app->make(Areas::class);
        });


        $this->app->singleton('antares.notifications', function ($app) {
            return Notification::getInstance();
        });
        /**
         * @todo refactoring -> get dependencies from configuration
         */
        $this->app->bind('Antares\UI\UIComponents\Contracts\GridStack', 'Antares\UI\UIComponents\Adapter\GridStackAdapter');
        $this->app->bind('Antares\Tester\Contracts\Extractor', 'Antares\Tester\Adapter\ExtractAdapter');
        $this->app->bind('Antares\Tester\Contracts\ClassValidator', 'Antares\Tester\Validator\ClassValidator');
        $this->app->bind('Antares\Contracts\Http\Middleware\ModuleNamespaceResolver', 'Antares\Foundation\Http\Resolver\ModuleNamespaceResolver');

        $this->app['antares.logger.installed'] = false;

        $this->registerLogFilename();
    }

    /**
     * Registers log filename
     * 
     * @return void
     */
    protected function registerLogFilename()
    {
        $handlers = $this->app->make('log')->getMonolog()->getHandlers();
        foreach ($handlers as $handler) {
            if (!$handler instanceof RotatingFileHandler) {
                continue;
            }
            $filename = (php_sapi_name() === 'cli') ? 'laravel-cli' : 'laravel';
            $handler->setFilenameFormat($filename . '-{date}.log', 'Y-m-d');
        }

        return;
    }

    /**
     * Register the request resolver
     *
     * @return void
     */
    protected function registerRequestResolver()
    {
        $this->app->singleton('antares.request', function($app) {
            return new Request();
        });
    }

    /**
     * Register the service provider for Extension Finder.
     *
     * @return void
     */
    protected function registerWidgetsFinder()
    {
        $this->app->singleton('antares.ui-components.finder', function($app) {
            $config = [
                'path.app'  => $app->make('path'),
                'path.base' => $app->make('path.base'),
            ];

            return new Finder($app->make('files'), $config);
        });
    }

    /**
     * Register the service provider for Extension Finder.
     *
     * @return void
     */
    protected function registerWidgetsTemplateFinder()
    {
        $this->app->singleton('antares.ui-components.templates.finder', function ($app) {
            return new TemplateFinder($app);
        });
    }

    /**
     * Register the service provider for foundation.
     *
     * @return void
     */
    protected function registerFoundation()
    {
        $this->app['antares.installed']               = false;
        $this->app['antares.ui-components.installed'] = false;
        $this->app->singleton('antares.app', function ($app) {
            return new Foundation($app);
        });
    }

    /**
     * Register the service provider for site.
     *
     * @return void
     */
    protected function registerMeta()
    {
        $this->app->singleton('antares.meta', function () {
            return new Meta();
        });
    }

    /**
     * Register additional events for application.
     *
     * @return void
     */
    protected function registerEvents()
    {
        $this->app->terminating(function () {
            $this->app->make('events')->fire('antares.done');
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $router = $this->app->make(Router::class);
        $path   = realpath(__DIR__ . '/../../');
        $this->addConfigComponent('antares/foundation', 'antares/foundation', $path . '/resources/config');
        $this->addLanguageComponent('antares/foundation', 'antares/foundation', $path . '/resources/lang');
        $this->assignAreaTemplate();


        $this->appendWidgetsDefaultConfig();
        if (!$this->app->routesAreCached()) {
            require "{$path}/src/routes.php";
        }
        $router->aliasMiddleware('antares.forms', FormMiddleware::class);
        $router->aliasMiddleware('antares.ui-components', UIComponentsMiddleware::class);
        $this->runtime();
        $this->bootNotificationVariables();


        $this->app->make('events')->fire('antares.ready');
        $this->app->make('view')->composer(['antares/foundation::account.index', 'antares/logger::admin.devices.*'], \Antares\Users\Http\Handlers\AccountPlaceholder::class);

        $this->app->make('events')->listen('antares.after.load-service-providers', function() {
            $widgets = $this->app->make('antares.ui-components.finder')->detectRoutes();

            $widgets->each(function($widget) {
                if (class_exists($widget)) {
                    $widget::routes();
                }
            });
            return true;
        });

        $this->app->make('events')->subscribe(AfterExtensionOperation::class);
    }

    /**
     * Assign area template based on current area
     */
    protected function assignAreaTemplate()
    {
        $path = realpath(__DIR__ . '/../../');
        $area = area();
        if (!is_null($area) && is_dir(resource_path('views' . DIRECTORY_SEPARATOR . $area))) {
            $this->addViewComponent($area, 'antares/foundation', $path . '/resources/views');
        }
        $this->addViewComponent('default', 'antares/foundation', $path . '/resources/views');
    }

    /**
     * runtime application configuration
     */
    protected function runtime()
    {
        $filesystem = app(Filesystem::class);
        $files      = $filesystem->glob(storage_path('license') . '/*.key');
        if (!empty($files)) {
            app('antares.memory')->make('runtime')->push('instance_key', $filesystem->get($files[0]));
        }
    }

    /**
     * boot widgets routing
     */
    protected function bootWidgetsRouting()
    {
        try {
            $widgets = $this->app->make('antares.ui-components.finder')->detectRoutes();
            $widgets->each(function($widget) {
                $widget::routes();
            });
            return true;
        } catch (Exception $e) {
            Log::emergency($e);
            return false;
        }
    }

    /**
     * appends widgets default config into main foundation config container
     */
    protected function appendWidgetsDefaultConfig()
    {
        $widgetsPath = realpath(app_path() . '/../src/components/widgets');
        $this->addConfigComponent('antares/widgets', 'antares/widgets', "{$widgetsPath}/resources/config");
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['antares.app', 'antares.installed', 'antares.meta'];
    }

    /**
     * appends notification variables
     */
    protected function bootNotificationVariables()
    {
        if (!app('antares.installed')) {
            return;
        }
        $registry = app('antares.memory')->make('registry');
        $vars     = [
            'brand.name' => [
                'value'       => brand_name(),
                'description' => 'Brand name'
            ],
        ];
        $emails   = $registry->get('email');
        if (!empty($emails)) {
            foreach (array_except($emails, ['password']) as $name => $value) {
                $vars['email.' . $name] = ['value' => $value];
            }
        }
        $this->app->make('antares.notifications')->push([
            'foundation' => [
                'variables' => array_merge($vars, config('antares/foundation::notification.variables'))
            ]
        ]);
    }

}
