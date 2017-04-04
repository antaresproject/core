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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Extension;

use Antares\Contracts\Extension\Dispatcher as DispatcherContract;
use Antares\Contracts\Extension\Factory as FactoryContract;
use Antares\Extension\Traits\DispatchableTrait;
use Illuminate\Contracts\Container\Container;
use Antares\Extension\Traits\OperationTrait;
use Antares\Extension\Processor\NameSpacer;
use Antares\Contracts\Extension\SafeMode;
use Illuminate\Support\Facades\Route;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Antares\Memory\ContainerTrait;
use Antares\Model\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Exception;

class Factory implements FactoryContract
{

    use ContainerTrait,
        DispatchableTrait,
        OperationTrait;

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Dispatcher instance.
     *
     * @var \Antares\Contracts\Extension\Dispatcher
     */
    protected $dispatcher;

    /**
     * List of extensions.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $extensions;

    /**
     * List of routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * The event dispatcher implementation.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * Construct a new Application instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @param  \Antares\Contracts\Extension\Dispatcher  $dispatcher
     * @param  \Antares\Contracts\Extension\SafeMode  $mode
     */
    public function __construct(Container $app, DispatcherContract $dispatcher, SafeMode $mode)
    {
        $this->app        = $app;
        $this->events     = $this->app->make('events');
        $this->dispatcher = $dispatcher;
        $this->extensions = new Collection();
        $this->mode       = $mode;
    }

    /**
     * Detect all extensions.
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function detect()
    {
        $this->app->make('events')->fire('antares.extension: detecting');
        $extensions = $this->finder()->detect();
        $this->memory->put('extensions.available', $extensions->all());
        return $extensions;
    }

    /**
     * saves all components from memory
     */
    public function finish()
    {
        if (!is_null($this->memory)) {
            return $this->memory->finish();
        }
    }

    /**
     * Get extension finder.
     *
     * @return \Antares\Contracts\Extension\Finder
     */
    public function finder()
    {
        return $this->app->make('antares.extension.finder');
    }

    /**
     * Get an option for a given extension.
     *
     * @param  string  $name
     * @param  string  $option
     * @param  mixed   $default
     *
     * @return mixed
     */
    public function option($name, $option, $default = null)
    {
        if (!isset($this->extensions[$name])) {
            return value($default);
        }

        return Arr::get($this->extensions[$name], $option, $default);
    }

    /**
     * Check whether an extension has a writable public asset.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function permission($name)
    {
        $finder   = $this->finder();
        $memory   = $this->memory;
        $basePath = rtrim($memory->get("extensions.available.{$name}.path", $name), '/');
        $path     = $finder->resolveExtensionPath("{$basePath}/public");

        return $this->isWritableWithAsset($name, $path);
    }

    /**
     * Publish an extension.
     * @param  string
     * @return void
     */
    public function publish($name)
    {
        $this->app->make('antares.publisher.migrate')->extension($name);
        $this->app->make('antares.publisher.asset')->extension($name);

        $this->app->make('events')->fire('antares.publishing', [$name]);
        $this->app->make('events')->fire("antares.publishing: {$name}");
    }

    /**
     * uninstall extension
     * @param string
     */
    public function uninstall($name)
    {
        $this->app->make('antares.publisher.migrate')->uninstall($name);
    }

    /**
     * deletes an extension.
     * @param  string  $name
     * @param  string  $path
     * @return bool
     */
    public function delete($name)
    {
        try {

            $this->app->make('antares.publisher.asset')->delete($name);
            $finder    = $this->finder();
            $memory    = $this->memory;
            $basePath  = rtrim($memory->get("extensions.available.{$name}.path", $name), '/');
            $directory = $finder->resolveExtensionPath($basePath);

            if (starts_with($basePath, 'vendor::antares/modules')) {
                list($category, $package) = explode('/', $name);
                NameSpacer::getInstance($category, $package, 'delete')->rewrite();
            }

            $fileSystem = new Filesystem();
            $fileSystem->deleteDirectory($directory);
            $this->app->make('antares.memory')->make('component.default')->getHandler()->forceForgetCache();
            return true;
        } catch (\Exception $e) {
            Log::emergency($e);
            return false;
        }
    }

    /**
     * Register an extension.
     *
     * @param  string  $name
     * @param  string  $path
     *
     * @return bool
     */
    public function register($name, $path)
    {
        return $this->finder()->registerExtension($name, $path);
    }

    /**
     * Get extension route handle.
     *
     * @param  string   $name
     * @param  string   $default
     *
     * @return \Antares\Contracts\Extension\RouteGenerator
     */
    public function route($name, $default = '/')
    {
        !$this->booted() && $this->app->make('Antares\Extension\Bootstrap\LoadExtension')->bootstrap($this->app);
        if (!isset($this->routes[$name])) {
            $key                 = "antares/extension::handles.{$name}";
            $this->routes[$name] = new \Antares\Extension\RouteGenerator($this->app->make('config')->get($key, $default), $this->app->make('request'));
        }

        return $this->routes[$name];
    }

    /**
     * Check whether an extension has a writable public asset.
     *
     * @param  string  $name
     * @param  string  $path
     *
     * @return bool
     */
    protected function isWritableWithAsset($name, $path)
    {
        $files      = $this->app->make('files');
        $publicPath = $this->app->make('path.public');
        $targetPath = "{$publicPath}/packages/{$name}";

        if (Str::contains($name, '/') && !$files->isDirectory($targetPath)) {
            list($vendor) = explode('/', $name);
            $targetPath = "{$publicPath}/packages/{$vendor}";
        }

        $isWritable = $files->isWritable($targetPath);

        if ($files->isDirectory($path) && !$isWritable) {
            return false;
        }

        return true;
    }

    /**
     * fills extension collection
     * 
     * @return Collection
     */
    public function fill()
    {
        return ($this->extensions->isEmpty()) ? ($this->extensions = $this->extensions->make($this->detect())) : $this->extensions;
    }

    /**
     * verify whether extension is active
     * 
     * @param String $name
     * @return boolean
     */
    public function isActive($name)
    {
        try {
            $activated = antares('memory')->get('extensions.active');
        } catch (\Exception $ex) {
            return false;
        }

        if (is_null($activated)) {
            return false;
        }
        $extensions = array_keys($activated);
        foreach ($extensions as $extension) {
            if (str_contains($extension, $name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * extensions path getter
     * 
     * @return array
     */
    public function getActiveExtensionByPath($path)
    {
        $active = app('antares.memory')->make('component')->get('extensions.active', []);
        if (empty($active)) {
            return false;
        }
        $finder = $this->finder();

        foreach ($active as $name => $values) {
            if (starts_with($path, $finder->resolveExtensionPath($values['path']))) {
                return $name;
            }
        }

        return false;
    }

    /**
     * get extension path by name
     * 
     * @param String $name
     * @return String
     * @throws Exception
     */
    public function getExtensionPathByName($name)
    {
        $available = antares('memory')->get('extensions.available');
        if (is_null($available)) {
            return false;
        }
        foreach ($available as $extension) {
            if (array_get($extension, 'name') !== $name) {
                continue;
            }
            return $this->finder()->resolveExtensionPath($extension['path']);
        }
    }

    /**
     * get actual extension name based on route
     * 
     * @return String
     */
    public function getActualExtension()
    {
        if (is_null(Route::getCurrentRoute())) {
            return false;
        }
        $action = Route::getCurrentRoute()->getActionName();
        if ($action === 'Closure') {
            return false;
        }
        preg_match("/.+?(?=\\\)(.*)\Http/", $action, $matches);

        return empty($matches) ? false : strtolower(trim($matches[1], '\\'));
    }

    public function getExtensionOptions($name)
    {
        $component = Component::query()->where('name', $name)->first();

        if ($component) {
            return $component->options;
        }

        throw new Exception('No extension found for name: ' . $name . '.');
    }

}
