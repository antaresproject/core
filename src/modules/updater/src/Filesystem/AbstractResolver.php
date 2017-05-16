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






namespace Antares\Updater\Filesystem;

use Illuminate\Filesystem\Filesystem;
use Antares\Updater\Contracts\Migrator;
use Illuminate\Contracts\Foundation\Application;

abstract class AbstractResolver
{

    /**
     * path of compressed migration file
     *
     * @var String
     */
    protected $path;

    /**
     * messages container
     *
     * @var array
     */
    protected $messages = [];

    /**
     * filesystem handler
     *
     * @var Filesystem 
     */
    protected $filesystem;

    /**
     * version info
     *
     * @var String
     */
    protected $version;

    /**
     * migrator instance
     *
     * @var Migrator 
     */
    protected $migrator;

    /**
     * content resolver adapter
     *
     * @var Adapter\CurlAdapter
     */
    protected $adapter;

    /**
     * does migration has errors
     *
     * @var boolean 
     */
    protected $hasError = false;

    /**
     * eloquent version model
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * constructing
     * 
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem, Migrator $migrator, Application $app)
    {
        $config           = config('antares/updater::resolver');
        $this->filesystem = $filesystem;
        $this->migrator   = $migrator;
        $this->adapter    = $app->make(array_get($config, 'adapters.default.model'));
        $this->model      = $app->make(array_get($config, 'model'));
    }

    /**
     * set path of compressed migration file
     * 
     * @param String $path
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * update version setter
     * 
     * @param String $version
     * @return \Antares\Updater\Filesystem\Resolver
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->validate();
    }

    /**
     * messages getter
     * 
     * @return array
     */
    public function getMessages()
    {
        return array_merge($this->messages, $this->migrator->getMessages());
    }

    /**
     * migration has error
     * 
     * @return boolean
     */
    public function hasError()
    {
        return $this->hasError;
    }

    /**
     * get path
     * 
     * @return String
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * decompress migration file with files mapping
     */
    abstract public function resolve();

    /**
     * run migration
     */
    abstract public function migrate();

    /**
     * validate compressed file with update script
     */
    abstract protected function validate();
}
