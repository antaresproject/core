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






namespace Antares\Updater\Strategy;

use Antares\Updater\Contracts\Migrator as MigratorContract;
use Illuminate\Filesystem\Filesystem;
use Antares\Updater\Strategy\Adapter\MigratorAdapter;
use Antares\Updater\Strategy\Adapter\SeederAdapter;
use Illuminate\Support\Facades\Log;
use Exception;

class Migrator implements MigratorContract
{

    /**
     * migrator instance
     *
     * @var Migrator
     */
    protected $migratorAdapter;

    /**
     * seeder instance
     *
     * @var Seeder
     */
    protected $seederAdapter;

    /**
     * filesystem handler
     *
     * @var Filesystem 
     */
    protected $filesystem;

    /**
     * migration files list
     *
     * @var array 
     */
    protected $migrationList = [];

    /**
     * messages container
     *
     * @var array
     */
    protected $messages = [];

    /**
     * list of files from update package
     *
     * @var array 
     */
    protected $filesList = [];

    /**
     * has error flag
     *
     * @var boolean
     */
    protected $hasError = false;

    /**
     * constructor
     * 
     * @param Application $app
     */
    public function __construct(Filesystem $filesystem, MigratorAdapter $migratorAdapter, SeederAdapter $seederAdapter)
    {
        $this->migratorAdapter = $migratorAdapter;
        $this->seederAdapter   = $seederAdapter;
        $this->filesystem      = $filesystem;
    }

    /**
     * create migration as strategy
     * 
     * @param type $path
     */
    public function migrate($path)
    {
        return $this->extract($path)->import();
    }

    /**
     * migration runner
     * 
     * @return \Antares\Updater\Strategy\Migrator
     */
    protected function import()
    {

        if (empty($this->migrationList)) {
            return $this;
        }

        array_push($this->messages, 'Preparing to run migration...');
        foreach ($this->migrationList as $vendor => $pathes) {
            $this->down($pathes);
        }
        array_push($this->messages, 'Migration starting...');
        try {
            foreach ($this->migrationList as $vendor => $pathes) {
                $this->up($pathes);
            }
        } catch (Exception $e) {
            Log::emergency($e);
            array_push($this->messages, $e->getMessage());
            $this->hasError = true;
        }


        return $this;
    }

    /**
     * down migration
     * 
     * @param array $list
     */
    protected function down($list)
    {
        foreach ($list as $source) {
            $result         = (str_contains($source, 'seed')) ? $this->seederAdapter->down($source) : $this->migratorAdapter->down($source);
            $this->messages = array_merge($this->messages, $result->getNotes());
        }
    }

    /**
     * run migration of single package
     * 
     * @param array $list
     */
    protected function up($list)
    {
        foreach ($list as $source) {
            $result         = (str_contains($source, 'seed')) ? $this->seederAdapter->seed($source) : $this->migratorAdapter->run($source);
            $this->messages = array_merge($this->messages, $result->getNotes());
        }
    }

    /**
     * create pathname by migration file
     * 
     * @param String $relativePath
     * @return String
     */
    private function path($relativePath)
    {
        if (!str_contains($relativePath, 'resources' . DIRECTORY_SEPARATOR . 'database')) {
            return false;
        }
        $return    = [];
        $extracted = explode(DIRECTORY_SEPARATOR, $relativePath);
        foreach ($extracted as $directory) {
            if ($directory == 'resources') {
                break;
            }
            array_push($return, $directory);
        }
        return implode(DIRECTORY_SEPARATOR, $return);
    }

    /**
     * manual sort for migration files
     * 
     * @param array $migrationList
     * @return boolean
     */
    private function sort(&$migrationList = array())
    {
        array_push($this->messages, 'Start sorting migration repository using component key->value pair.');
        if (empty($migrationList)) {
            return false;
        }
        $return = [];
        foreach ($migrationList as $name => $value) {
            $value = array_unique($value);
            if (str_contains($name, 'core')) {
                $return[$name] = $value;
            }
        }

        array_push($this->messages, 'Merging sorted migration repository.');
        $migrationList = array_merge($return, array_except($migrationList, array_keys($return)));
        return $migrationList;
    }

    /**
     * extract migration files from import package
     * 
     * @param String $path
     * @return \Antares\Updater\Strategy\Migrator
     */
    protected function extract($path)
    {
        array_push($this->messages, 'Start extracting migration data from repository.');
        $this->filesList = $this->filesystem->allFiles($path);
        if (empty($this->filesList)) {
            array_push($this->messages, 'Migration repository is empty. Updater job queue is stopped.');
            return $this;
        }

        $migrations = [];
        foreach ($this->filesList as $file) {
            if (($name = $this->path($file->getRelativePath())) !== false) {
                $return    = [];
                $extracted = explode(DIRECTORY_SEPARATOR, $file->getRealPath());
                foreach ($extracted as $directory) {
                    if ($directory == 'resources') {
                        break;
                    }
                    array_push($return, $directory);
                }
                $source              = $file->getRealPath();
                $migrations[$name][] = dirname($source);
                $migrations[$name]   = array_unique($migrations[$name]);
            }
        }
        $this->sort($migrations);
        $this->migrationList = $migrations;
        return $this;
    }

    /**
     * retrive all messages from message container
     * 
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * has error getter
     * 
     * @return boolean
     */
    public function hasError()
    {
        return $this->hasError;
    }

}
