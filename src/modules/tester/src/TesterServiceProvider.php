<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Tester;

use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\Support\Providers\Traits\AliasesProviderTrait;
use Illuminate\Http\Response;
use Antares\Memory\Provider;

class TesterServiceProvider extends ModuleServiceProvider
{

    use AliasesProviderTrait;

    /**
     * List of widgets aliases.
     *
     * @var array
     */
    protected $aliases = ['antares.tester' => 'Antares\Tester\Factory',];

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Tester\Http\Controllers\Admin';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/tester';

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->bindContracts();
        $this->app->singleton('antares.tester', function ($app) {
            $dispatcher = new Dispatcher($app, $app->make('router'), $app->make('request'));
            return new Factory($dispatcher, new Response());
        });
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

        $path = realpath(__DIR__ . '/../');
        $this->addConfigComponent('antares/tester', 'antares/tester', "{$path}/resources/config");
        $this->addLanguageComponent('antares/tester', 'antares/tester', "{$path}/resources/lang");

        $this->addViewComponent('antares/tester', 'antares/tester', "{$path}/resources/views");

        $this->bootMemory();

        $this->extendMemory();
    }

    /**
     * booting events
     */
    protected function bootMemory()
    {
        $this->app->make('antares.acl')->make('antares/tester')->attach($this->app->make('antares.platform.memory'));
    }

    /**
     * binds memory to tester
     */
    protected function extendMemory()
    {
        $app    = $this->app;
        $config = $this->app->make('config');
        $app->make('antares.memory')->extend('tests', function ($app, $name) use($config) {
            $handler = new Memory\Handler($name, $config->get('antares/tester::memory'), $app);
            return new Provider($handler);
        });
    }

}
