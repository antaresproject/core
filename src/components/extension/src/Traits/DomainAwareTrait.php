<?php

declare(strict_types=1);

namespace Antares\Extension\Traits;

use Antares\Extension\RouteGenerator;
use Antares\Foundation\Application;

/**
 * Class DomainAwareTrait
 * @property Application $app
 */
trait DomainAwareTrait
{

    /**
     * Register domain awareness from configuration.
     *
     * @return void
     */
    public function registerDomainAwareness()
    {
        $this->app->afterResolving(function (RouteGenerator $generator, Application $app) {
            $generator->setBaseUrl($app->make('config')->get('app.url'));
        });
    }

}
