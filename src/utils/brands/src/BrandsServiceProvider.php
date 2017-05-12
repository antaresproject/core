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

namespace Antares\Brands;

use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\Brands\Contracts\BrandsRepositoryContract;
use Antares\Brands\Http\Middleware\BrandsMiddleware;
use Antares\Brands\Repositories\BrandsRepository;
use Antares\Brands\Http\Handlers\BrandsPane;
use Antares\Brands\BrandStyler;
use Illuminate\Routing\Router;

class BrandsServiceProvider extends ModuleServiceProvider
{

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Brands\Http\Controllers\Admin';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/brands';

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'antares.ready: admin' => ['Antares\Brands\Composers\BrandPlaceHolder@onBootExtension']
    ];

    /**
     * registering component
     */
    public function register()
    {
        parent::register();
        $this->registerFacades();
        $this->registerRepositories();
        $this->registerBrandsTeller();
        $this->registerBrandEloquent();
    }

    /**
     * registering component facades
     */
    protected function registerFacades()
    {
        $this->app->singleton(BrandStyler::class);
        $this->app->alias(BrandStyler::class, 'brand-styler');
    }

    /**
     * registering repositories
     */
    protected function registerRepositories()
    {
        $this->app->bind(BrandsRepositoryContract::class, BrandsRepository::class);
    }

    /**
     * register service provider.
     * 
     * @return void
     */
    protected function registerBrandsTeller()
    {
        $this->app->singleton(BrandsTeller::class);
        $this->app->alias(BrandsTeller::class, 'antares.brands');
    }

    /**
     * register the service provider for brand.
     * 
     * @return void
     */
    protected function registerBrandEloquent()
    {
        $this->app->bind('antares.brand', function () {
            return new Model\Brands();
        });
    }

    /**
     * Boot components
     */
    public function boot()
    {
        parent::boot();
        $this->app->make(Router::class)->pushMiddlewareToGroup('web', BrandsMiddleware::class);
        $this->app->make('view')->composer(['antares/foundation::brands.email', 'antares/foundation::brands.edit'], BrandsPane::class);
    }

    /**
     * boot the service provider.
     * 
     * @return void
     */
    public function bootExtensionComponents()
    {
        if (app_installed()) {
            $this->setDefaultBrand();
        }
        $path = __DIR__ . '/../';
        $this->addViewComponent('antares/brands', 'antares/brands', "{$path}/resources/views");
        $this->loadBackendRoutesFrom(__DIR__ . "/routes.php");
    }

    /**
     * default brand setting when not attach
     */
    protected function setDefaultBrand()
    {
        $memory  = $this->app->make('antares.memory');
        $primary = $memory->make('primary');
        $model   = app('antares.brand');


        if (is_null($primary->get('brand.default'))) {
            $primary->put('brand.default', $model::defaultBrand()->id);
        }
        $default  = $this->getBrand();
        $template = current(array_get($default, 'templates'));
        unset($default['templates']);
        $registry = $memory->make('registry');
        $registry->put('brand.configuration', $default);
        $registry->put('brand.configuration.template', $template);
    }

    /**
     * Brand getter
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getBrand()
    {
        $baseUrl = basename(url('/'));
        $appUrl  = basename(env('APP_URL'));
        if (!str_contains($baseUrl, $appUrl)) {
            $return = app('antares.brand')->default()->first()->toArray();
        } else {
            $model = app('antares.brand')->newQuery()->whereHas('options', function($query) use($baseUrl) {
                        $query->where('url', 'like', $baseUrl . '%');
                    })->with(['options', 'templates' => function($query) {
                            $query->where('area', area());
                        }])->first();
            $return = !is_null($model) ? $model->toArray() : app('antares.brand')->default()->first()->toArray();
        }
        return $return;
    }

}
