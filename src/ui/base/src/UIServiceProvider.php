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
 * @package    UI
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI;

use Antares\Support\Providers\ServiceProvider;
use Antares\UI\Navigation\Breadcrumbs\Manager;
use Illuminate\Contracts\Events\Dispatcher;
use Antares\UI\Navigation\Factory;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;
use Knp\Menu\MenuFactory;
use View;

class UIServiceProvider extends ServiceProvider
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
        $this->app->singleton('antares.widget', function ($app) {
            return new WidgetManager($app);
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
        $this->addConfigComponent('antares/widget', 'antares/widget', $path . '/config');

        $this->app->singleton(Factory::class, function () {
            $renderOptions  = (array) config('antares/widget::navigation.menu.render', []);
            $url            = $this->app['url'];
            $dispatcher     = $this->app->make(Dispatcher::class);

            $factory = new MenuFactory();
            $matcher = new Matcher();
            $matcher->addVoter(new UriVoter($url->current()));
            $matcher->addVoter(new UriVoter($url->full()));

            return new Factory($factory, $matcher, $dispatcher, $renderOptions);
        });

        $this->app->singleton(Manager::class);

        /* @var $manager Manager */
        $manager = $this->app->make(Manager::class);

        //antares/foundation::layouts/antares/partials/_head_webpack
        //antares/foundation::layouts.antares.partials._breadcrumbs

        View::composer('antares/foundation::layouts/antares/partials/_head_webpack', function() use($manager) {
            if($manager->isEnabled()) {
                $manager->generate();
                $manager->setupMeta();
            }
        });

        View::composer('antares/foundation::layouts.antares.partials._breadcrumbs', function(\Illuminate\View\View $view) use($manager) {
            if($manager->isEnabled()) {
                $manager->generate();
                $view->with('new_breadcrumbs', true)->with('breadcrumbs', $manager->render());
            }
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['antares.widget', Factory::class, Manager::class];
    }

}
