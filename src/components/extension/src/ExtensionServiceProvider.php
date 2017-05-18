<?php

declare(strict_types=1);

namespace Antares\Extension;

use Antares\Extension\Bootstrap\LoadExtension;
use Antares\Extension\Composer\Handler;
use Antares\Extension\Repositories\ComponentsRepository;
use Antares\Extension\Repositories\ConfigRepository;
use Antares\Support\Providers\ServiceProvider as BaseServiceProvider;
use Composer\Package\Loader\ArrayLoader;
use Composer\Package\Loader\LoaderInterface;

class ExtensionServiceProvider extends BaseServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LoaderInterface::class, ArrayLoader::class);

        $this->registerExtensionConfig();
        $this->registerExtensionFinder();
        $this->registerExtensionManager();
        $this->registerExtensionDispatcher();
        $this->registerExtensionBootstrap();
        $this->registerComposerHandler();
        $this->registerComponentsRepository();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $path = __DIR__ . '/../';

        $this->addConfigComponent('antares/extension', 'antares/extension', $path . '/resources/config');
    }

    protected function registerExtensionConfig()
    {
        $this->app->singleton(ConfigRepository::class, function() {
            $config = (array) config('antares/extension::config', []);

            return new ConfigRepository($config, base_path(), public_path());
        });

        $this->app->alias(ConfigRepository::class, 'antares.extension.config');
    }

    protected function registerExtensionFinder()
    {
        $this->app->singleton(FilesystemFinder::class);
        $this->app->alias(FilesystemFinder::class, 'antares.extension.finder');
    }

    protected function registerExtensionManager()
    {
        $this->app->singleton(Manager::class);
        $this->app->alias(Manager::class, 'antares.extension');
    }

    protected function registerExtensionBootstrap()
    {
        $this->app->singleton(LoadExtension::class);
        $this->app->alias(LoadExtension::class, 'antares.extension.bootstrap');

        $this->app->singleton(Loader::class);
    }

    protected function registerExtensionDispatcher()
    {
        $this->app->singleton(Dispatcher::class);
        $this->app->alias(Dispatcher::class, 'antares.extension.dispatcher');
    }

    protected function registerComposerHandler() {
        $this->app->singleton(Handler::class, function() {
            $config = (array) config('antares/extension::config.composer.parameters', []);

            return new Handler($config);
        });
    }

    protected function registerComponentsRepository() {
        $this->app->singleton(ComponentsRepository::class, function() {
            $branches = (array) config('components.branches', []);
            $required = (array) config('components.required', []);
            $optional = (array) config('components.optional', []);

            return new ComponentsRepository($branches, $required, $optional);
        });
    }

}
