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


namespace Antares\Config;

use Illuminate\Filesystem\Filesystem;

class FileLoader implements LoaderInterface
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The default configuration path.
     *
     * @var string
     */
    protected $defaultPath;

    /**
     * All of the named path hints.
     *
     * @var array
     */
    protected $hints = [];

    /**
     * A cache of whether namespaces and groups exists.
     *
     * @var array
     */
    protected $exists = [];

    /**
     * Create a new file configuration loader.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $defaultPath
     */
    public function __construct(Filesystem $files, $defaultPath)
    {
        $this->files       = $files;
        $this->defaultPath = $defaultPath;
    }

    /**
     * Load the given configuration group.
     *
     * @param  string  $environment
     * @param  string  $group
     * @param  string  $namespace
     *
     * @return array
     */
    public function load($environment, $group, $namespace = null)
    {
        $items = [];

        $path = $this->getPath($namespace);

        if (is_null($path)) {
            return $items;
        }

        $file = "{$path}/{$group}.php";

        if ($this->files->exists($file)) {
            $items = $this->files->getRequire($file);
        }

        $file = "{$path}/{$environment}/{$group}.php";

        if ($this->files->exists($file)) {
            $items = $this->mergeEnvironment($items, $file);
        }

        return $items;
    }

    /**
     * Merge the items in the given file into the items.
     *
     * @param  array   $items
     * @param  string  $file
     *
     * @return array
     */
    protected function mergeEnvironment(array $items, $file)
    {
        return array_replace_recursive($items, $this->files->getRequire($file));
    }

    /**
     * Determine if the given group exists.
     *
     * @param  string  $group
     * @param  string  $namespace
     *
     * @return bool
     */
    public function exists($group, $namespace = null)
    {
        $key = $group . $namespace;

        if (!isset($this->exists[$key])) {
            $path = $this->getPath($namespace);

            if (is_null($path)) {
                return $this->exists[$key] = false;
            }

            $file = "{$path}/{$group}.php";

            $this->exists[$key] = $this->files->exists($file);
        }

        return $this->exists[$key];
    }

    /**
     * Apply any cascades to an array of package options.
     *
     * @param  string  $env
     * @param  string  $package
     * @param  string  $group
     * @param  array   $items
     *
     * @return array
     */
    public function cascadePackage($env, $package, $group, $items)
    {

        $file = "packages/{$package}/{$group}.php";

        if ($this->files->exists($path = $this->defaultPath . '/' . $file)) {
            $items = array_merge($items, $this->getRequire($path));
        }

        $path = $this->getPackagePath($env, $package, $group);

        if ($this->files->exists($path)) {
            $items = array_merge($items, $this->getRequire($path));
        }

        return $items;
    }

    /**
     * Get the package path for an environment and group.
     *
     * @param  string  $env
     * @param  string  $package
     * @param  string  $group
     *
     * @return string
     */
    protected function getPackagePath($env, $package, $group)
    {
        $file = "packages/{$package}/{$env}/{$group}.php";

        return $this->defaultPath . '/' . $file;
    }

    /**
     * Get the configuration path for a namespace.
     *
     * @param  string  $namespace
     *
     * @return string
     */
    protected function getPath($namespace)
    {
        if (is_null($namespace)) {
            return $this->defaultPath;
        } elseif (isset($this->hints[$namespace])) {
            return $this->hints[$namespace];
        }
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     *
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        $this->hints[$namespace] = $hint;
    }

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->hints;
    }

    /**
     * Get a file's contents by requiring it.
     *
     * @param  string  $path
     *
     * @return mixed
     */
    protected function getRequire($path)
    {
        return $this->files->getRequire($path);
    }

    /**
     * Get the Filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }

}
