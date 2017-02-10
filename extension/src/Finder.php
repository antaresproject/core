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

use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Antares\Contracts\Support\ManifestRuntimeException;
use Antares\Contracts\Extension\Finder as FinderContract;

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
     * Default manifest options.
     *
     * @var array
     */
    protected $manifestOptions = [
        'name'        => null,
        'full_name'   => null,
        'description' => null,
        'author'      => null,
        'url'         => null,
        'version'     => '>0',
        'config'      => [],
        'autoload'    => [],
        'provides'    => [],
        'options'     => [],
    ];

    /**
     * List of reserved name.
     *
     * @var array
     */
    protected $reserved = [
        'antares',
        'resources',
        'antares/asset',
        'antares/auth',
        'antares/debug',
        'antares/extension',
        'antares/facile',
        'antares/foundation',
        'antares/html',
        'antares/memory',
        'antares/messages',
        'antares/model',
        'antares/notifier',
        'antares/optimize',
        'antares/platform',
        'antares/resources',
        'antares/support',
        'antares/testbench',
        'antares/view',
        'antares/widget',
    ];

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
        $app          = rtrim($config['path.app'], '/');
        $base         = rtrim($config['path.base'], '/');

        $this->paths = [
            "{$app}",
            "{$base}/src/core/*",
            "{$base}/src/components/*",
            "{$base}/src/modules/*",
            "{$base}/src/modules/domains/*",
            "{$base}/src/modules/products/*",
            "{$base}/src/modules/fraud/*",
            "{$base}/src/modules/addons/*",
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
        $path = rtrim($path, '/');

        if (!in_array($path, $this->paths)) {
            $this->paths[] = $path;
        }

        return $this;
    }

    /**
     * resolve component / module namespace by file path
     * 
     * @param String $path
     * @param boolean $asPackage
     * @return String
     */
    public function resolveNamespace($path, $asPackage = false)
    {
        $file       = new File($path);
        $pathInfo   = array_filter(explode(DIRECTORY_SEPARATOR, trim(str_replace([base_path(), 'src'], '', $file->getRealPath()), DIRECTORY_SEPARATOR)));
        $prefix     = 'antares';
        $namespaces = [];
        foreach ($pathInfo as $name) {
            if ($name == 'core') {
                $name = ($asPackage) ? 'foundation' : $name;
                array_push($namespaces, $name);
                break;
            }
            if ($name == 'app') {
                array_push($namespaces, 'foundation');
                break;
            }
            if ($name == 'src') {
                break;
            }
            if (in_array($name, ['components', 'modules'])) {
                continue;
            }
            array_push($namespaces, $name);
        }
        return $prefix . '/' . implode('/', $namespaces);
    }

    /**
     * Detect available extensions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function detect()
    {
        $extensions = [];
        $pattern    = $this->config['pattern'];
        foreach ($this->paths as $key => $path) {
            $manifests = $this->files->glob($this->resolveExtensionPath("{$path}/{$pattern}"));
            is_array($manifests) || $manifests = [];
            foreach ($manifests as $manifest) {
                $name = (is_numeric($key) ? $this->guessExtensionNameFromManifest($manifest, $path) : $key);

                if (!is_null($name)) {
                    $extensions[$name] = $this->getManifestContents($manifest);
                }
            }
        }

        return new Collection($extensions);
    }

    /**
     * Get manifest contents.
     *
     * @param  string  $manifest
     *
     * @return array
     *
     * @throws \Antares\Contracts\Support\ManifestRuntimeException
     */
    public function getManifestContents($manifest)
    {
        $path       = $sourcePath = $this->guessExtensionPath($manifest);
        $jsonable   = json_decode($this->files->get($manifest), true);

        if (is_null($jsonable)) {
            throw new ManifestRuntimeException("Cannot decode file [{$manifest}]");
        }

        isset($jsonable['path']) && $path = $jsonable['path'];

        $paths = [
            'path'        => rtrim($path, '/'),
            'source-path' => rtrim($sourcePath, '/'),
        ];

        return array_merge($paths, $this->generateManifestConfig($jsonable));
    }

    /**
     * Generate a proper manifest configuration for the extension. This
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
        $manifest['provides'] = Arr::get($jsonable, 'provide', $manifest['provides']);

        return $manifest;
    }

    /**
     * Guess extension name from manifest.
     *
     * @param  string  $manifest
     * @param  string  $path
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function guessExtensionNameFromManifest($manifest, $path)
    {
        if (rtrim($this->config['path.app'], '/') === rtrim($path, '/')) {
            return 'app';
        }

        list($vendor, $package) = $namespace = $this->resolveExtensionNamespace($manifest);

        if (is_null($vendor) && is_null($package)) {
            return;
        }

        $name = trim(implode('/', $namespace));

        return $this->validateExtensionName($name);
    }

    /**
     * Guess extension path from manifest file.
     *
     * @param  string  $path
     *
     * @return string
     */
    public function guessExtensionPath($path)
    {
        $path = str_replace($this->config['pattern'], '', $path);
        $app  = rtrim($this->config['path.app'], '/');
        $base = rtrim($this->config['path.base'], '/');

        return str_replace(
                ["{$app}/", "{$base}/vendor/", "{$base}/"], $this->config['paths'], $path
        );
    }

    /**
     * Register the extension.
     *
     * @param  string  $name
     * @param  string  $path
     *
     * @return bool
     */
    public function registerExtension($name, $path)
    {
        $this->paths[$name] = rtrim($path, '/');

        return true;
    }

    /**
     * Resolve extension namespace name from manifest.
     *
     * @param  string  $manifest
     *
     * @return array
     */
    public function resolveExtensionNamespace($manifest)
    {
        $vendor   = null;
        $package  = null;
        $manifest = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $manifest);
        $fragment = explode(DIRECTORY_SEPARATOR, $manifest);

        if (array_pop($fragment) == 'manifest.json') {
            $package = array_pop($fragment);
            $vendor  = array_pop($fragment);
        }

        return [$vendor, $package];
    }

    /**
     * Resolve extension path.
     *
     * @param  string  $path
     * @return string
     */
    public function resolveExtensionPath($path)
    {
        $app  = rtrim($this->config['path.app'], '/');
        $base = rtrim($this->config['path.base'], '/');
        return str_replace(
                ['app::', 'vendor::antares', 'base::'], ["{$app}/", "{$base}/src", "{$base}/"], $path
        );
    }

    /**
     * Validate extension name.
     *
     * @param  string  $name
     * @return string
     * @throws \RuntimeException
     */
    public function validateExtensionName($name)
    {
        if (in_array($name, $this->reserved)) {
            throw new RuntimeException("Unable to register reserved name [{$name}] as extension.");
        }

        return $name;
    }

    /**
     * removes slug path name from extension real path
     * 
     * @param String $path
     * @return String
     */
    public function resolveExtensionVendorPath($path)
    {
        return str_replace($this->config['paths'], '', $path);
    }

}
