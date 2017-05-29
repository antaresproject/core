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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents;

use Antares\UI\UIComponents\Contracts\Finder as FinderContract;
use Antares\UI\UIComponents\Adapter\AbstractTemplate;
use Antares\UI\UIComponents\Registry\Registry;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Exception;

class Finder implements FinderContract
{

    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Application and base path configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * List of paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * Construct a new finder.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  array  $config
     */
    public function __construct(Filesystem $files, array $config)
    {
        $this->files  = $files;
        $this->config = $config;
        $base         = rtrim($config['path.base'], '/');

        $this->paths = [
            "{$base}/src/core/src/modules/*/src",
            "{$base}/src/modules/*/src",
        ];
    }

    /**
     * Add a new path to finder.
     *
     * @param  string  $path
     *
     * @return $this
     */
    public function addPath($path)
    {
        $trimmed = rtrim($path, '/');
        if (!in_array($trimmed, $this->paths)) {
            $this->paths[] = $trimmed;
        }
        return $this;
    }

    /**
     * Detects app ui components paths
     * 
     * @return array
     */
    protected function detectUiComponents()
    {
        $components  = [];
        $factory     = app('antares.extension');
        $directories = [];

        foreach ($this->paths as $path) {
            try {
                $directories = array_merge($directories, $this->files->directories($path));
            } catch (Exception $ex) {
                continue;
            }
        }
        $inner = [];
        foreach ($directories as $index => $directory) {

            $classBasename = class_basename($directory);
            if (!in_array($classBasename, ['Widgets', 'UiComponents']) or ! $factory->getActiveExtensionByPath($directory)) {
                unset($directories[$index]);
                continue;
            }
            if (!empty($componentDirectory = $this->files->directories($directory))) {
                $inner = array_merge($inner, $componentDirectory);
            }
        }

        $directories = array_merge($directories, $inner);
        foreach ($directories as $directory) {
            $components = array_merge($components, $this->files->files($directory));
        }
        return $components;
    }

    /**
     * Detect available ui components.
     *
     * @return \Illuminate\Support\Collection
     */
    public function detect()
    {
        $components = [];
        $files      = $this->detectUiComponents();

        foreach ($files as $file) {
            $name = $this->files->name($file);

            $params = $this->resolveUIComponentParams($name, $file);

            if (!$params) {
                continue;
            }

            $components[snake_case($name)] = $params;
        }
        return new Collection($components);
    }

    /**
     * Detects ui component routes
     * 
     * @return Collection
     */
    public function detectRoutes()
    {
        $components = $this->detectUiComponents();

        $return = [];
        foreach ($components as $component) {
            $name      = $this->files->name($component);
            $namespace = $this->resolveUIComponentNamespace($this->files->get($component));
            $return[]  = $namespace . '\\' . $name;
        }
        return new Collection($return);
    }

    /**
     * Resolves ui component params
     * 
     * @param String $name
     * @param String $file
     * @return string
     */
    protected function resolveUIComponentParams($name, $file)
    {
        $namespace = $this->resolveUIComponentNamespace($this->files->get($file));
        if (!class_exists($namespace . '\\' . $name)) {
            return false;
        }
        $instance   = app($namespace . '\\' . $name);
        $hasWidgets = Registry::isRegistered('ui-components');

        if (!$hasWidgets) {
            $collection = new Collection([$instance]);
        } else {
            $collection = Registry::get('ui-components');
            $collection->push($instance);
        }

        $this->resolveDisabledUIComponent($instance);
        $this->resolveViewedUIComponent($instance);
        Registry::set('ui-components', $collection);
        $attributes = $instance->getAttributes();

        return array_except($attributes, ['id']);
    }

    /**
     * Resolves which ui components should be disabled on which view
     * 
     * @param \Antares\UI\UIComponents\Adapter\AbstractTemplate $component
     * @return boolean|void
     */
    protected function resolveDisabledUIComponent(AbstractTemplate $component, $keyname = 'ui-components.disabled')
    {
        $disabled = $component->getDisabled();
        if (empty($disabled)) {
            return false;
        }
        $classname = get_class($component);

        view()->composer($disabled, function() use($keyname, $classname) {
            $hasWidgets = Registry::isRegistered($keyname);
            if (!$hasWidgets) {
                $collection = new Collection([$classname]);
            } else {
                $collection = Registry::get($keyname);
                $collection->push($classname);
            }
            Registry::set($keyname, $collection);
        });
        return;
    }

    /**
     * Resolves viewed ui components
     * 
     * @param \Antares\UI\UIComponents\Adapter\AbstractTemplate $component
     * @return boolean|void
     */
    protected function resolveViewedUIComponent(AbstractTemplate $component, $keyname = 'ui-components.viewed')
    {
        $viewed = $component->views();
        if (empty($viewed)) {
            return false;
        }
        $classname = get_class($component);
        view()->composer($viewed, function() use($keyname, $classname) {
            $collection = !Registry::isRegistered($keyname) ? new Collection() : Registry::get($keyname);
            if (!$collection->contains($classname)) {
                $collection->push($classname);
            }
            Registry::set($keyname, $collection);
        });
    }

    /**
     * Resolves ui component source path
     * 
     * @param String $file
     * @return String
     */
    protected function resolvePath($file)
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR, 'vendor::', $file);
    }

    /**
     * Trying to get file namespace from file content
     * 
     * @param String $src
     * @param numeric $i
     * @return String | null
     */
    public function resolveUIComponentNamespace($src, $i = 0)
    {
        $tokens       = token_get_all($src);
        $count        = count($tokens);
        $namespace    = '';
        $namespace_ok = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace_ok = true;
                        $namespace    = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
        }
        return (!$namespace_ok) ? null : $namespace;
    }

    /**
     *
     * @param  string  $path
     * @return string
     */
    public function resolveUIComponentPath($path)
    {
        $app  = rtrim($this->config['path.app'], '/');
        $base = rtrim($this->config['path.base'], '/');
        return str_replace(
                ['app::', 'vendor::', 'base::'], ["{$app}/", "{$base}/src/", "{$base}/"], $path
        );
    }

}
