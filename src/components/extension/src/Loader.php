<?php

namespace Antares\Extension;

use Antares\Extension\Contracts\ExtensionContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Antares\Foundation\Application;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcherContract;

class Loader
{
    /**
     * Application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The event dispatcher implementation.
     *
     * @var EventDispatcherContract
     */
    protected $events;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * List of compiled services.
     *
     * @var array
     */
    protected $compiled = [];

    /**
     * List of cached manifest.
     *
     * @var array
     */
    protected $manifest = [];

    /**
     * The path to the manifest file.
     *
     * @var string
     */
    protected $manifestPath;

    /**
     * Loader constructor.
     * @param Application $app
     * @param EventDispatcherContract $events
     * @param Filesystem $files
     */
    public function __construct(Application $app, EventDispatcherContract $events, Filesystem $files)
    {
        $this->app    = $app;
        $this->events = $events;
        $this->files  = $files;

        $this->manifestPath = $this->app->getCachedExtensionServicesPath();
    }

    /**
     * Registers extension providers.
     *
     * @param ExtensionContract $extension
     * @throws \Exception
     */
    public function registerExtensionProviders(ExtensionContract $extension) {
        $filePath = $extension->getPath() . '/providers.php';

        if($this->files->exists($filePath)) {
            $providers = (array) $this->files->getRequire($filePath);

            $this->provides($providers);
        }
    }

    /**
     * Load available service providers.
     *
     * @param  array $provides
     * @return void
     */
    public function provides(array $provides)
    {
        $services = [];

        foreach ($provides as $provider) {
            if (! isset($this->manifest[$provider])) {
                $services[$provider] = $this->recompileProvider($provider);
            } else {
                $services[$provider] = $this->manifest[$provider];
            }
        }

        $this->dispatch($services);
    }

    /**
     * Recompile provider by reviewing the class configuration.
     *
     * @param  string $provider
     * @return array
     */
    protected function recompileProvider(string $provider) : array
    {
        $instance = $this->app->resolveProvider($provider);

        $type = $instance->isDeferred() ? 'Deferred' : 'Eager';

        return $this->{"register{$type}ServiceProvider"}($provider, $instance);
    }

    /**
     * Register all deferred service providers.
     *
     * @param $services
     * @return void
     */
    protected function dispatch(array $services)
    {
        foreach ($services as $provider => $options) {
            $this->loadDeferredServiceProvider($provider, $options);
            $this->loadEagerServiceProvider($provider, $options);
            $this->loadQueuedServiceProvider($provider, $options);

            unset($options['instance']);

            $this->compiled[$provider] = $options;
        }
    }

    /**
     * Load the service provider manifest JSON file.
     *
     * @return array
     */
    public function loadManifest() : array
    {
        $this->manifest = [];

        // The service manifest is a file containing a JSON representation of every
        // service provided by the application and whether its provider is using
        // deferred loading or should be eagerly loaded on each request to us.
        if ($this->files->exists($this->manifestPath)) {
            return $this->manifest = $this->files->getRequire($this->manifestPath);
        }

        return $this->manifest;
    }

    /**
     * Determine if the manifest should be compiled.
     *
     * @return bool
     */
    public function shouldRecompile() : bool
    {
        return array_keys($this->manifest) !== array_keys($this->compiled);
    }

    /**
     * Write the service manifest file to disk.
     *
     * @return void
     */
    public function writeManifest()
    {
        if ($this->shouldRecompile()) {
            $this->writeManifestFile($this->compiled);
        }
    }

    /**
     * Write an empty service manifest file to disk.
     *
     * @return void
     */
    public function writeFreshManifest()
    {
        $this->writeManifestFile($this->manifest = []);
    }

    /**
     * Write the manifest file.
     *
     * @param  array  $manifest
     *
     * @return void
     */
    protected function writeManifestFile(array $manifest = [])
    {
        $this->files->put($this->manifestPath, '<?php return '.var_export($manifest, true).';');
    }

    /**
     * Register deferred service provider.
     *
     * @param  string $provider
     * @param  ServiceProvider $instance
     * @return array
     */
    protected function registerDeferredServiceProvider($provider, ServiceProvider $instance) : array
    {
        $deferred = [];

        foreach ($instance->provides() as $provide) {
            $deferred[$provide] = $provider;
        }

        return [
            'instance' => $instance,
            'eager'    => false,
            'when'     => $instance->when(),
            'deferred' => $deferred,
        ];
    }

    /**
     * Register eager service provider.
     *
     * @param  string $provider
     * @param  ServiceProvider $instance
     * @return array
     */
    protected function registerEagerServiceProvider($provider, ServiceProvider $instance) : array
    {
        return [
            'instance' => $instance,
            'eager'    => true,
            'when'     => [],
            'deferred' => [],
        ];
    }

    /**
     * Load deferred service provider.
     *
     * @param  string  $provider
     * @param  array  $options
     *
     * @return void
     */
    protected function loadDeferredServiceProvider($provider, array $options)
    {
        if ($options['eager']) {
            return ;
        }

        $this->app->addDeferredServices($options['deferred']);
    }

    /**
     * Load eager service provider.
     *
     * @param  string  $provider
     * @param  array  $options
     *
     * @return void
     */
    protected function loadEagerServiceProvider($provider, array $options)
    {
        if (! $options['eager']) {
            return ;
        }

        $instance = Arr::get($options, 'instance', $provider);

        $this->app->register($instance);
    }

    /**
     * Load queued service provider.
     *
     * @param  string  $provider
     * @param  array  $options
     *
     * @return void
     */
    protected function loadQueuedServiceProvider($provider, array $options)
    {
        $listeners = (array) Arr::get($options, 'when', []);

        foreach ($listeners as $listen) {
            $this->events->listen($listen, function () use ($provider, $options) {
                $instance = Arr::get($options, 'instance', $provider);

                $this->app->register($instance);
            });
        }
    }
}