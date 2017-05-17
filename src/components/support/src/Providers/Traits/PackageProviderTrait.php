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


namespace Antares\Support\Providers\Traits;

use ReflectionClass;
use Antares\Contracts\Config\PackageRepository;

trait PackageProviderTrait
{

    /**
     * Register the package's config component namespaces.
     *
     * @param  string  $package
     * @param  string  $namespace
     * @param  string  $path
     *
     * @return void
     */
    public function addConfigComponent($package, $namespace, $path)
    {
        if ($this->hasPackageRepository()) {
            $this->app->make('config')->package($package, $path, $namespace);
        }
    }

    /**
     * Register the package's language component namespaces.
     *
     * @param  string  $package
     * @param  string  $namespace
     * @param  string  $path
     *
     * @return void
     */
    public function addLanguageComponent($package, $namespace, $path)
    {
        $this->app->make('translator')->addNamespace($namespace, $path);
    }

    /**
     * Register the package's view component namespaces.
     *
     * @param  string  $package
     * @param  string  $namespace
     * @param  string  $path
     *
     * @return void
     */
    public function addViewComponent($package, $namespace, $path)
    {
        $files = $this->app->make('files');
        $view  = $this->app->make('view');
        foreach ($this->getAppViewPaths($package) as $appView) {
            if ($files->isDirectory($appView)) {
                $view->addNamespace($namespace, $appView);
            }
        }

        $view->addNamespace($namespace, $path);
    }

    /**
     * Register the package's component namespaces.
     *
     * @param  string  $package
     * @param  string  $namespace
     * @param  string  $path
     *
     * @return void
     */
    public function package($package, $namespace = null, $path = null)
    {
        $namespace = $this->getPackageNamespace($package, $namespace);
        $files     = $this->app->make('files');

        $path = $path ? : $this->guessPackagePath();

        if ($files->isDirectory($config = $path . '/config')) {
            $this->addConfigComponent($package, $namespace, $config);
        }


        if ($files->isDirectory($lang = $path . '/lang')) {
            $this->addLanguageComponent($package, $namespace, $lang);
        }


        if ($files->isDirectory($views = $path . '/views')) {
            $this->addViewComponent($package, $namespace, $views);
        }
    }

    /**
     * Guess the package path for the provider.
     *
     * @return string
     */
    public function guessPackagePath()
    {
        $path = (new ReflectionClass($this))->getFileName();

        return realpath(dirname($path) . '/../../');
    }

    /**
     * Determine the namespace for a package.
     *
     * @param  string  $package
     * @param  string  $namespace
     *
     * @return string
     */
    protected function getPackageNamespace($package, $namespace)
    {
        if (is_null($namespace)) {
            list(, $namespace) = explode('/', $package);
        }

        return $namespace;
    }

    /**
     * Get the application package view paths.
     *
     * @param  string  $package
     *
     * @return array
     */
    protected function getAppViewPaths($package)
    {
        return array_map(function ($path) use ($package) {
            return "{$path}/{$package}";
        }, $this->app->make('config')->get('view.paths'));
    }

    /**
     * Has package repository available.
     *
     * @return bool
     */
    protected function hasPackageRepository()
    {
        return ($this->app->make('config') instanceof PackageRepository);
    }

    /**
     * Boot under Laravel setup.
     *
     * @param  string  $path
     *
     * @return void
     */
    protected function bootUsingLaravel($path)
    {
        
    }

}
