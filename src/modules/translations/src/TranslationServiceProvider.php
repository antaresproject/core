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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Translations;

use Antares\Translations\Http\Handlers\TranslationsBreadcrumbMenu;
use Antares\Translations\Http\Handlers\LanguagesBreadcrumbMenu;
use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\Updater\Http\Handlers\TranslationsPane;
use Antares\Translations\Listener\AccountListener;

class TranslationServiceProvider extends ModuleServiceProvider
{

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Translations\Http\Controllers\Admin';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/translations';

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'antares.ready: admin' => ['Antares\Translations\Composers\LanguageSelector'],
    ];

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {
        $app                                   = $this->app;
        $app['antares.translations.installed'] = true;
        $this->bindContracts();

        $this->app->bind('languages', function() use($app) {
            return new Languages($app);
        });

        $this->app->singleton('translator', function($app) {
            $loader = $app->make('translation.loader');
            $locale = $app->make('config')->get('app.locale');
            $trans  = new Translator($loader, $locale);
            $trans->setFallback($app->make('config')->get('app.fallback_locale'));
            if ($app->bound('translation-manager')) {
                $trans->setTranslationManager($app->make('translation-manager'));
            }
            return $trans;
        });
    }

    /**
     * Boot extension routing.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $path = __DIR__;
        $this->loadBackendRoutesFrom("{$path}/routes.php");
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function bootExtensionComponents()
    {
        $path = __DIR__ . '/../';
        $this->addConfigComponent('antares/translations', 'antares/translations', "{$path}/resources/config");
        $this->addLanguageComponent('antares/translations', 'antares/translations', "{$path}/resources/lang");
        $this->addViewComponent('antares/translations', 'antares/translations', "{$path}/resources/views");
        $this->bootMemory();
        $this->attachMenu(TranslationsBreadcrumbMenu::class);
        $this->attachMenu(LanguagesBreadcrumbMenu::class);
        view()->composer(['antares/translations::admin.translation.index'], TranslationsPane::class);

        app(AccountListener::class)->listenForm()->listenFormSave();
    }

    /**
     * booting events
     */
    protected function bootMemory()
    {
        $this->app->make('antares.acl')->make($this->routeGroup)->attach(
                $this->app->make('antares.platform.memory')
        );
    }

}
