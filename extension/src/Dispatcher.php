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

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Config\Repository as Config;
use Antares\Contracts\Extension\Finder as FinderContract;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Antares\Contracts\Extension\Dispatcher as DispatcherContract;

class Dispatcher implements DispatcherContract
{

    /**
     * Config Repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Finder instance.
     *
     * @var \Antares\Contracts\Extension\Finder
     */
    protected $finder;

    /**
     * Provider instance.
     *
     * @var \Antares\Extension\ProviderRepository
     */
    protected $provider;

    /**
     * List of extensions to be boot.
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * Construct a new Application instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \Antares\Contracts\Extension\Finder  $finder
     * @param  \Antares\Extension\ProviderRepository  $provider
     */
    public function __construct(Config $config, EventDispatcher $dispatcher, Filesystem $files, FinderContract $finder, ProviderRepository $provider)
    {
        $this->config     = $config;
        $this->dispatcher = $dispatcher;
        $this->files      = $files;
        $this->finder     = $finder;
        $this->provider   = $provider;
    }

    /**
     * Register the extension.
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return void
     */
    public function register($name, array $options)
    {
        $handles = Arr::get($options, 'config.handles');
        if (!is_null($handles)) {

            $this->config->set("antares/extension::handles.{$name}", $handles);
        }
        $services = Arr::get($options, 'provides', []);

        !empty($services) && $this->provider->provides($services);

        $this->extensions[$name] = $options;
        $this->start($name, $options);
    }

    /**
     * Boot all extensions.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->extensions as $name => $options) {
            $this->fireEvent($name, $options, 'booted');
        }
    }

    /**
     * Start the extension.
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return void
     */
    public function start($name, array $options)
    {
        $file   = $this->files;
        $finder = $this->finder;

        $base     = rtrim($options['path'], '/');
        $source   = rtrim(Arr::get($options, 'source-path', $base), '/');
        $autoload = Arr::get($options, 'autoload', []);

        foreach ($this->getAutoloadFiles($autoload) as $path) {
            $path = str_replace(
                    ['source-path::', 'app::/'], ["{$source}/", 'app::'], $path
            );
            $path = $finder->resolveExtensionPath($path);

            if ($file->isFile($path)) {
                $file->getRequire($path);
            }
        }

        $this->fireEvent($name, $options, 'started');
    }

    /**
     * Shutdown an extension.
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return void
     */
    public function finish($name, array $options)
    {
        $this->fireEvent($name, $options, 'done');
    }

    /**
     * Fire events.
     *
     * @param  string  $name
     * @param  array   $options
     * @param  string  $type
     *
     * @return void
     */
    protected function fireEvent($name, $options, $type = 'started')
    {
        $this->dispatcher->fire("extension.{$type}", [$name, $options]);
        $this->dispatcher->fire("extension.{$type}: {$name}", [$options]);
    }

    /**
     * Get list of available paths for the extension.
     *
     * @param  array  $autoload
     *
     * @return array
     */
    protected function getAutoloadFiles(array $autoload)
    {
        $resolver = function ($path) {
            if (Str::contains($path, '::')) {
                return $path;
            }

            return 'source-path::' . ltrim($path, '/');
        };

        $paths = array_map($resolver, $autoload);

        return array_merge(
                $paths, ['source-path::src/antares.php', 'source-path::antares.php']
        );
    }

}
