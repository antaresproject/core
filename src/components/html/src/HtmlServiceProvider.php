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

namespace Antares\Html;

use Antares\Html\Memory\Config as MemoryConfig;
use Antares\Support\Providers\ServiceProvider;
use Antares\Html\Form\Factory as FormFactory;
use Antares\Html\Provider\Provider;
use Antares\Html\Memory\Handler;

class HtmlServiceProvider extends ServiceProvider
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
        $this->registerHtmlBuilder();

        $this->registerFormBuilder();

        $this->registerAntaresFormBuilder();

        $this->registerCustomfieldsFinder();

        $this->app->alias('html', 'Antares\Html\HtmlBuilder');
        $this->app->alias('form', 'Antares\Html\FormBuilder');
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder()
    {
        $this->app->singleton('html', function ($app) {
            return new HtmlBuilder($app->make('url'));
        });
    }

    /**
     * Register Customfields finder instance
     *
     * @return void
     */
    protected function registerCustomfieldsFinder()
    {

        $this->app->singleton('customfields', function () {
            return new CustomfieldsFinder();
        });
    }

    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app) {
            $form = new FormBuilder($app->make('html'), $app->make('url'));
            return $form->setSessionStore($app->make('session.store'));
        });
    }

    /**
     * Register the Antares\Form builder instance.
     *
     * @return void
     */
    protected function registerAntaresFormBuilder()
    {
        $this->app->singleton('Antares\Contracts\Html\Form\Control', 'Antares\Html\Form\Control');

        $this->app->singleton('Antares\Contracts\Html\Form\Template', function ($app) {
            $class = $app->make('config')->get('antares/html::form.presenter', 'Antares\Html\Form\BootstrapThreePresenter');

            return $app->make($class);
        });

        $this->app->singleton('antares.form', function ($app) {
            return new FormFactory($app);
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        $path = realpath(__DIR__ . '/../resources');

        $this->addConfigComponent('antares/html', 'antares/html', $path . '/config');
        $this->addViewComponent('html', 'antares/html', $path . '/views');

        $this->app->make('events')->listen('before.form.render', Events\BeforeFormRender::class);

        $memory = $this->app->make('antares.memory');

        $memory->extend('collector', function ($app) {
            $config     = $app->make('config');
            $connection = $config->get('antares/html::form.memory.default.connections.cache');
            $driver     = $app->make('cache')->driver($connection);
            $handler    = new Handler('collector', $config->get('antares/html::form.memory.default', [
                        'model' => 'Antares\Html\Model\Form',
                        'cache' => false,
                        'crypt' => false
                    ]), $app, $driver);
            return new Provider($handler);
        });

        $memory->extend('forms-config', function ($app) {
            $config     = $app->make('config');
            $connection = $config->get('antares/html::form.memory.form-config.connections.cache');
            $driver     = $app->make('cache')->driver($connection);
            $handler    = new MemoryConfig('forms-config', $config->get('antares/html::form.memory.form-config'), $app, $driver);
            return new Provider($handler);
        });
        listen('antares.form: ready', function() {
            app('antares.asset')->container('antares/foundation::application')->add('webpack_forms_basic', '/webpack/forms_basic.js', ['app_cache']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['html', 'form', 'antares.form', 'antares.form.control'];
    }

}
