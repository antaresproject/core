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


namespace Antares\Publisher\Publishing;

use Illuminate\Filesystem\Filesystem;

abstract class Publisher
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The destination of the config files.
     *
     * @var string
     */
    protected $publishPath;

    /**
     * The path to the application's packages.
     *
     * @var string
     */
    protected $packagePath;

    /**
     * Create a new publisher instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $publishPath
     */
    public function __construct(Filesystem $files, $publishPath)
    {
        $this->files       = $files;
        $this->publishPath = $publishPath;
    }

    /**
     * Get the source directory to publish.
     *
     * @param  string  $package
     * @param  string  $packagePath
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    abstract protected function getSource($package, $packagePath);

    /**
     * Publish files from a given path.
     *
     * @param  string  $package
     * @param  string  $source
     *
     * @return bool
     */
    public function publish($package, $source)
    {
        $name        = trim(str_replace(['modules', 'components'], '', $package), '/');
        $destination = $this->getDestinationPath($name);
        $this->makeDestination($destination);
        return app('antares.asset.publisher')->publishAndPropagate($this->files->allFiles($source), $name);
    }

    /**
     * Publish the files for a package.
     *
     * @param  string  $package
     * @param  string  $packagePath
     *
     * @return bool
     */
    public function publishPackage($package, $packagePath = null)
    {
        $source = $this->getSource($package, $packagePath ? : $this->packagePath);

        return $this->publish($package, $source);
    }

    /**
     * Create the destination directory if it doesn't exist.
     *
     * @param  string  $destination
     *
     * @return void
     */
    protected function makeDestination($destination)
    {
        if (!$this->files->isDirectory($destination)) {
            $this->files->makeDirectory($destination, 0777, true);
        }
    }

    /**
     * Determine if a given package has already been published.
     *
     * @param  string  $package
     *
     * @return bool
     */
    public function alreadyPublished($package)
    {
        return $this->files->isDirectory($this->getDestinationPath($package));
    }

    /**
     * Get the target destination path for the files.
     *
     * @param  string  $package
     *
     * @return string
     */
    public function getDestinationPath($package)
    {
        return $this->publishPath . "/packages/antares/{$package}";
    }

    /**
     * Set the default package path.
     *
     * @param  string  $packagePath
     *
     * @return void
     */
    public function setPackagePath($packagePath)
    {
        $this->packagePath = $packagePath;
    }

}
