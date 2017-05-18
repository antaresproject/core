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

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Fluent;
use Illuminate\Support\Arr;
use RuntimeException;

class TemplateManifest
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
    public function __construct(Filesystem $files, array $config, $path)
    {
        $path        = rtrim($path, '/');
        $this->files = $files;
        if ($files->exists($manifest    = "{$path}/" . $config['manifest_pattern'])) {
            $jsonable = json_decode($files->get($manifest), true);

            if (is_null($jsonable)) {
                throw new RuntimeException("Ui component template [{$path}]: cannot decode {$config['manifest_pattern']} file");
            }

            $this->items            = new Fluent($this->generateManifestConfig($jsonable));
            $this->items['path']    = $path;
            $previewExists          = file_exists(implode(DIRECTORY_SEPARATOR, [$config['public'], $config['public_path'], $config['dir'], $config['preview_pattern']]));
            $this->items['preview'] = $previewExists ? $config['public_path'] . '/' . $config['dir'] . '/' . $config['preview_pattern'] : $config['preview_default'];
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
     * Generate a proper manifest configuration for the template.
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
