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


namespace Antares\View\Theme;

use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Filesystem\Filesystem;

class Manifest
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Theme configuration.
     *
     * @var \Illuminate\Support\Fluent
     */
    protected $items;

    /**
     * Default manifest options.
     *
     * @var array
     */
    protected $manifestOptions = [
        'package'     => null,
        'name'        => null,
        'uid'         => null,
        'description' => null,
        'author'      => null,
        'autoload'    => [],
        'type'        => [],
    ];

    /**
     * Load the theme.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $path
     *
     * @throws \RuntimeException
     */
    public function __construct(Filesystem $files, $path)
    {
        $path        = rtrim($path, '/');
        $this->files = $files;

        if ($files->exists($manifest = "{$path}/theme.json")) {
            $jsonable = json_decode($files->get($manifest), true);

            if (is_null($jsonable)) {
                                throw new RuntimeException(
                "Theme [{$path}]: cannot decode theme.json file"
                );
            }

            $this->items = new Fluent($this->generateManifestConfig($jsonable));

            $this->items['uid']  = $this->parseThemeNameFromPath($path);
            $this->items['path'] = $path;
        }
    }

    /**
     * Get single attribute.
     *
     * @param  string  $key
     * @param  mixed|null  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->items->get($key, $default);
    }

    /**
     * Get collection.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * Generate a proper manifest configuration for the theme. This
     * would allow other part of the application to use this configuration
     * to migrate, load service provider as well as preload some
     * configuration.
     *
     * @param  array  $jsonable
     *
     * @return array
     */
    protected function generateManifestConfig(array $jsonable)
    {
        $manifest = [];

                foreach ($this->manifestOptions as $key => $default) {
            $manifest["{$key}"] = Arr::get($jsonable, $key, $default);
        }

        return $manifest;
    }

    /**
     * Get theme name from path.
     *
     * @param  string  $path
     *
     * @return string
     */
    protected function parseThemeNameFromPath($path)
    {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        $path = explode(DIRECTORY_SEPARATOR, $path);

        return array_pop($path);
    }

    /**
     * Magic method to get items by key.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (!isset($this->items->{$key})) {
            return;
        }

        return $this->items->get($key);
    }

    /**
     * Magic Method to check isset by key.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->items->{$key});
    }

}
