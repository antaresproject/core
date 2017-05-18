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


namespace Antares\View\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

class LoadCurrentTheme
{

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     *
     * @return void
     */
    public function bootstrap(Application $app)
    {
        if (app('antares.installed')) {
            //app('antares.license.response')->validate();
        }
        $this->setCurrentTheme($app);
        $this->setThemeResolver($app);
    }

    /**
     * Set current theme for request.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     *
     * @return void
     */
    protected function setCurrentTheme(Application $app)
    {
        $memory = $app->make('antares.memory')->makeOrFallback();
        $app->make('antares.theme')->setTheme($memory->get('site.theme.frontend'));

        $events = $app->make('events');

        $events->listen('antares.started: admin', function () use ($app, $memory, $events) {
            $theme = $app->make('antares.theme');
            if (auth()->isAny(['member'])) {
                $theme->setTheme($memory->get('site.theme.frontend'));
            } else {
                $theme->setTheme($memory->get('site.theme.backend'));
            }
        });

        $events->listen('composing: *', function () use ($app) {
            $app->make('antares.theme')->boot();
        });
    }

    /**
     * Boot theme resolver.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     *
     * @return void
     */
    protected function setThemeResolver(Application $app)
    {
        if ($app->resolved('view')) {
            $app->make('antares.theme')->resolving();
        } else {
            $app->resolving('view', function () use ($app) {
                $app->make('antares.theme')->resolving();
            });
        }
    }

}
