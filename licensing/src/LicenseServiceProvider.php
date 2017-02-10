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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Licensing;

use Antares\Foundation\Support\Providers\ModuleServiceProvider;

class LicenseServiceProvider extends ModuleServiceProvider
{

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Licensing\Http\Controllers';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/licensing';

    /**
     * The fallback route prefix.
     *
     * @var string
     */
    protected $routePrefix = 'license';

    /**
     * bindable dependency injection params
     *
     * @var array
     */
    protected $di = [
        'Antares\Licensing\Contracts\LicensePresenter' => 'Antares\Licensing\Http\Presenters\LicensePresenter'
    ];

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;
        foreach ($this->di as $contract => $object) {
            $app->bind($contract, $object);
        }
        $this->registerLicensing();
    }

    /**
     * registering license factory
     */
    public function registerLicensing()
    {
        $this->app->singleton('antares.license', function ($app) {
            return $app->make('Antares\Licensing\Validator\Validator');
        });
        $this->app->singleton('antares.license.generator', function ($app) {
            return $app->make('Antares\Licensing\Generator\Generator');
        });
        $this->app->singleton('antares.license.remote', function ($app) {
            return $app->make('Antares\Licensing\Validator\RemoteValidator');
        });
        $this->app->singleton('antares.license.response', function ($app) {
            return $app->make('Antares\Licensing\Validator\ResponseValidator');
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
        $this->afterExtensionLoaded(function () use ($path) {
            $this->loadFrontendRoutesFrom("{$path}/Http/frontend.php");
        });
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function bootExtensionComponents()
    {
        $path = __DIR__ . '/../';
        $this->addConfigComponent('antares/licensing', 'antares/licensing', "{$path}/resources/config");
        $this->addLanguageComponent('antares/licensing', 'antares/licensing', "{$path}/resources/lang");
        $this->addViewComponent('licensing', 'antares/licensing', "{$path}/resources/views");
    }

}
