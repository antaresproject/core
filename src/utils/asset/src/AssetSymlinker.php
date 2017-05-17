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

use Symfony\Component\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Exception;

class AssetSymlinker
{

    /**
     * The filesystem instance.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Publish path
     *
     * @var String
     */
    protected $publishPath = '';

    /**
     * Create a new asset publisher instance.
     *
     * @param Filesystem $files
     * @param string $publishPath
     */
    public function __construct(Filesystem $files, $publishPath = null)
    {
        $this->files       = $files;
        $this->publishPath = $publishPath;
    }

    /**
     * Publish path setter
     * 
     * @param String $publishPath
     * @return \Antares\Asset\AssetSymlinker
     */
    public function setPublishPath($publishPath)
    {
        $this->publishPath = $publishPath;
        return $this;
    }

    /**
     * Copy all assets from a given path to the publish path.
     *
     * @param string $name
     * @param string $source
     * @throws \RuntimeException
     * @return bool
     */
    public function publish($name, $source)
    {
        $relative    = str_replace(sandbox_path(), '', $this->publishPath) . "/{$name}";
        $destination = $this->publishPath . "/{$name}";
        try {
            $this->files->symlink($source, $destination);
        } catch (Exception $e) {
            Log::emergency($e);
        }
        return $relative;
    }

}
