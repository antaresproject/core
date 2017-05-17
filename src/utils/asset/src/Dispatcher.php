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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Asset;

use Illuminate\Support\Str;
use Antares\Html\HtmlBuilder;
use Illuminate\Filesystem\Filesystem;
use Antares\Support\Facades\Foundation;

class Dispatcher
{

    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Html builder instance.
     *
     * @var \Antares\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Dependency resolver instance.
     *
     * @var \Antares\Asset\DependencyResolver
     */
    protected $resolver;

    /**
     * Public path location.
     *
     * @var string
     */
    protected $path;

    /**
     * Use asset versioning.
     *
     * @var bool
     */
    public $useVersioning = false;

    /**
     * sandbox public build path
     *
     * @var String 
     */
    protected static $sandboxPath;

    /**
     * Create a new asset dispatcher instance.
     *
     * @param \Illuminate\Filesystem\Filesystem  $files
     * @param \Antares\Html\HtmlBuilder  $html
     * @param \Antares\Asset\DependencyResolver  $resolver
     * @param $path
     */
    public function __construct(Filesystem $files, HtmlBuilder $html, DependencyResolver $resolver, $path)
    {
        $this->files    = $files;
        $this->html     = $html;
        $this->resolver = $resolver;
        $this->path     = $path;
    }

    /**
     * Enable asset versioning.
     *
     * @return void
     */
    public function addVersioning()
    {
        $this->useVersioning = true;
    }

    /**
     * Disable asset versioning.
     *
     * @return void
     */
    public function removeVersioning()
    {
        $this->useVersioning = false;
    }

    /**
     * Dispatch assets by group.
     *
     * @param  string  $group
     * @param  array  $assets
     * @param  string|null  $prefix
     *
     * @return string
     */
    public function run($group, array $assets = [], $prefix = null)
    {
        $html = '';

        if (!isset($assets[$group]) || count($assets[$group]) == 0) {
            return $html;
        }

        is_null($prefix) || $this->path = rtrim($prefix, '/');

        foreach ($this->resolver->arrange($assets[$group]) as $data) {
            $html .= $this->asset($group, $data);
        }

        return $html;
    }

    /**
     * Get the HTML link to a registered asset.
     *
     * @param  string  $group
     * @param  array   $asset
     *
     * @return string
     */
    public function asset($group, $asset)
    {
        if (!isset($asset)) {
            return '';
        }

        $asset['source'] = $this->getAssetSourceUrl($asset['source'], $group);

        return call_user_func_array([$this->html, $group], [$asset['source'], $asset['attributes']]);
    }

    /**
     * Determine if path is local.
     *
     * @param  string  $path
     *
     * @return bool
     */
    protected function isLocalPath($path)
    {
        if (Str::startsWith($path, ['https://', 'http://', '//'])) {
            return false;
        }

        return (filter_var($path, FILTER_VALIDATE_URL) === false);
    }

    /**
     * Get asset source URL.
     *
     * @param  string  $source
     *
     * @return string
     */
    protected function getAssetSourceUrl($source, $group = null)
    {
        if (!$this->isLocalPath($file = $this->path . '/' . ltrim($source, '/'))) {
            return $file;
        }

        return $this->getAssetSourceUrlWithModifiedTime($source, $file, $group);
    }

    /**
     * Get asset source URL with Modified time.
     *
     * @param  string  $source
     * @param  string  $file
     *
     * @return string
     */
    protected function getAssetSourceUrlWithModifiedTime($source, $file, $group = null)
    {
        $this->getAssetSandboxSourceUrl($source, $group);
        if ($this->isLocalPath($source) && $this->useVersioning) {

            if (!empty($modified = $this->files->lastModified($file))) {
                $source = $source . "?{$modified}";
            }
        }

        return $source;
    }

    /**
     * create asset sandbox source url
     * 
     * @param String $source
     * @return boolean
     */
    protected function getAssetSandboxSourceUrl(&$source, $group = null)
    {
        if (is_null(self::$sandboxPath)) {
            if (!Foundation::bound('antares.version')) {
                self::$sandboxPath = '';
            }
            $sandboxMode = app('request')->get('sandbox');
            if ($sandboxMode and $group !== 'inline') {
                $publicPath = Foundation::make('Antares\Updater\Contracts\Requirements')->setVersion($sandboxMode)->getPublicPath();
                $path       = last(explode(DIRECTORY_SEPARATOR, $publicPath));
                $source     = $path . '/' . $source;
            }
            return false;
        }
        return self::$sandboxPath;
    }

    /**
     * Dispatch assets by group.
     *
     * @param  string  $group
     * @param  array  $assets
     * @param  string|null  $prefix
     *
     * @return array
     */
    public function scripts($group, array $assets = [], $prefix = null)
    {
        $scripts = [];

        if (!isset($assets[$group]) || count($assets[$group]) == 0) {
            return $scripts;
        }

        is_null($prefix) || $this->path = rtrim($prefix, '/');

        foreach ($this->resolver->arrange($assets[$group]) as $data) {
            $path = $this->getAssetSourceUrl($data['source']);

            !starts_with($path, '/') && $group !== 'inline' ? $path = '/' . $path : null;

            array_push($scripts, $path);
        }
        return $scripts;
    }

}
