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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
namespace Antares\Events;

use Antares\Foundation\Support\Providers\Traits\RouteProviderTrait;
use Antares\Support\Providers\ServiceProvider;

class EventsServiceProvider extends ServiceProvider
{
//
//    use RouteProviderTrait;

    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @var string|null
     */
    protected $routeGroup = 'antares/events';

    /**
     * @var String
     */
    protected $namespace = 'Antares\Events\Http\Controllers\Admin';

    /**
     * @return void
     */
    public function register()
    {
        echo '<pre>';
        dd(123242123);
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        echo '<pre>';
        dd(123242123);
        $path = realpath(__DIR__ . '/../resources');
        $this->addConfigComponent($this->routeGroup, $this->routeGroup, "{$path}/config");
        $this->addLanguageComponent($this->routeGroup, $this->routeGroup, "{$path}/lang");
        $this->addViewComponent($this->routeGroup, $this->routeGroup, "{$path}/views");
        if (!$this->app->routesAreCached()) {
            $this->loadBackendRoutesFrom(__DIR__ . "/routes.php");
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        echo '<pre>';
        dd(123242123);
        return [];
    }
}
