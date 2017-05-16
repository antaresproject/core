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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Strategy\Sandbox;

use Antares\Updater\Contracts\SandboxFiles;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use FilesystemIterator;
use Exception;

class Files extends AbstractStrategy implements SandboxFiles
{

    /**
     * filesystem instance
     *
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * config instance
     *
     * @var Repository
     */
    protected $config;

    /**
     * constructing
     * 
     * @param Filesystem $fileSystem
     * @param Repository $config
     */
    public function __construct(Filesystem $fileSystem, Repository $config)
    {
        $this->fileSystem = $fileSystem;
        $this->config     = $config->get('antares/updater::sandbox.files');
    }

    /**
     * copy app source files to builds directory 
     */
    public function copy()
    {
        set_time_limit(0);
        try {
            $this->copyPublic();
            $this->copyApplication();
            return true;
        } catch (Exception $e) {
            Log::emergency($e);
            $this->hasError = true;
            $this->note($e->getMessage());
            return false;
        }
    }

    /**
     * copying application source directory
     */
    protected function copyApplication()
    {
        $directories = $this->fileSystem->directories(base_path());
        $ignore      = array_get($this->config, 'ignore');
        $buildPath   = $this->getBuildPath();
        $ignore      = array_map(function($element) {
            return base_path($element);
        }, $ignore);
        $toCopy = array_diff($directories, $ignore);
        foreach ($toCopy as $directory) {
            $name = last(explode(DIRECTORY_SEPARATOR, $directory));
            $this->copyDirectory($directory, $buildPath . DIRECTORY_SEPARATOR . $name);
        }
    }

    /**
     * copying public directory
     */
    protected function copyPublic()
    {
        $buildPublicPath = $this->getPublicPath();
        $ignore          = [
            last(explode(DIRECTORY_SEPARATOR, $buildPublicPath))
        ];

        $filesystem  = app(Filesystem::class);
        $directories = $filesystem->directories(public_path());
        $mapped      = array_map(function($item) {
            return last(explode(DIRECTORY_SEPARATOR, $item));
        }, $directories);

        $additionalIgnore = array_filter($mapped, function($item) {
            return starts_with($item, 'build');
        });
        $ignore = array_unique(array_merge($ignore, $additionalIgnore));
        $this->copyDirectory(base_path('public'), $buildPublicPath . DIRECTORY_SEPARATOR, null, $ignore);
    }

    /**
     * Copy a directory from one location to another.
     *
     * @param  string  $directory
     * @param  string  $destination
     * @param  int     $options
     * @return bool
     */
    public function copyDirectory($directory, $destination, $options = null, array $ignore = [])
    {
        if (!$this->fileSystem->isDirectory($directory)) {
            return false;
        }
        $options = $options ? : FilesystemIterator::SKIP_DOTS;
        if (!$this->fileSystem->isDirectory($destination)) {
            $this->fileSystem->makeDirectory($destination, 0777, true);
        }
        $items = new FilesystemIterator($directory, $options);
        foreach ($items as $item) {
            try {
                $basename = $item->getBasename();
                if ($basename == '.git' OR in_array($basename, $ignore)) {
                    continue;
                }
                $target = $destination . '/' . $basename;


                if ($item->isDir()) {
                    $path = $item->getPathname();

                    if (!$this->copyDirectory($path, $target, $options, $ignore)) {
                        return false;
                    }
                } else {
                    if (!$this->fileSystem->copy($item->getPathname(), $target)) {
                        return false;
                    }
                }
            } catch (Exception $e) {
                continue;
            }
        }

        return true;
    }

    /**
     * rollbacking when error
     * 
     * @param String $version
     * @return \Antares\Updater\Strategy\Sandbox\Files
     */
    public function rollback($version = null)
    {
        $this->setVersion($version);
        $buildPath = $this->getBuildPath();

        if ($this->fileSystem->deleteDirectory($buildPath, true)) {
            $this->fileSystem->deleteDirectory($buildPath);
        }

        $publicPath = $this->getPublicPath();

        if ($this->fileSystem->deleteDirectory($publicPath, true)) {
            $this->fileSystem->deleteDirectory($publicPath);
        }
        return $this;
    }

}
