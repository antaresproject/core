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


namespace Antares\Publisher;

use Antares\Contracts\Publisher\FilePermissionException;
use Antares\Contracts\Publisher\Publisher;
use Antares\Publisher\Publishing\AssetPublisher;
use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Log;
use function public_path;

class AssetManager implements Publisher
{

    /**
     * Application instance.
     *
     * @var Container
     */
    protected $app;

    /**
     * Migrator instance.
     *
     * @var AssetPublisher
     */
    protected $publisher;

    /**
     * Construct a new instance.
     *
     * @param  Container  $app
     * @param  AssetPublisher  $publisher
     */
    public function __construct(Container $app, AssetPublisher $publisher)
    {
        $this->app       = $app;
        $this->publisher = $publisher;
    }

    /**
     * Run migration for an extension or application.
     *
     * @param  string  $name
     * @param  string  $destinationPath
     *
     * @return mixed
     */
    public function publish($name, $destinationPath)
    {
        return $this->publisher->publish($name, $destinationPath);
    }

    /**
     * Migrate extension.
     *
     * @param  string  $name
     *
     * @return mixed
     *
     * @throws FilePermissionException
     */
    public function extension($name)
    {
        if (is_null($path = $this->getPathFromExtensionName($name))) {
            return false;
        }

        try {
            return $this->publish($name, $path);
        } catch (Exception $e) {
            Log::emergency($e);
            throw new FilePermissionException("Unable to publish [{$path}].");
        }
    }

    /**
     * Migrate Antares.
     *
     * @return mixed
     *
     * @throws FilePermissionException
     */
    public function foundation()
    {
        $path = rtrim($this->app->make('path.base'), '/') . '/src/core/foundation/resources/public';

        if (!$this->app->make('files')->isDirectory($path)) {
            return false;
        }

        try {
            return $this->publish('antares/foundation', $path);
        } catch (Exception $e) {
            Log::emergency($e);
            throw new FilePermissionException("Unable to publish [{$path}].");
        }
    }

    /**
     * Get path from extension name.
     *
     * @param  string  $name
     *
     * @return string|null
     */
    protected function getPathFromExtensionName($name)
    {
        $basePath   = $this->app->make('antares.extension')->getExtensionPathByName($name);
        $paths      = ["{$basePath}/resources/public", "{$basePath}/public"];

        foreach ($paths as $path) {
            if ($this->app->make('files')->isDirectory($path)) {
                return $path;
            }
        }

        return;
    }

    /**
     * deletes published extension asset
     * @param String $name
     * @return boolean
     * @throws Exception
     */
    public function delete($name)
    {
        $path = public_path() . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . $name;

        if (!$this->app->make('files')->isDirectory($path)) {
            return false;
        }
        try {
            return $this->publisher->delete($path);
        } catch (Exception $ex) {
            Log::emergency($ex);
            throw new Exception("Unable to delete [{$path}].");
        }
    }

}
