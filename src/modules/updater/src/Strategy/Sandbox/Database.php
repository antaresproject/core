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

use Antares\Updater\BackupHandlers\Database\DatabaseBuilder;
use Antares\Updater\Contracts\Database as DatabaseContract;
use Symfony\Component\Filesystem\Filesystem;
use Illuminate\Contracts\Config\Repository;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class Database extends AbstractStrategy implements DatabaseContract
{

    /**
     * file of sql dump
     *
     * @var String
     */
    private $file;

    /**
     * current database configuration
     *
     * @var array
     */
    private $dbConfig;

    /**
     * database instance
     *
     * @var \Antares\Updater\BackupHandlers\Database\Databases\MySQLDatabase
     */
    protected $database;

    /**
     * sandbox database configuration container
     *
     * @var array 
     */
    protected $config;

    /**
     * Filesystem instance
     *
     * @var Filesystem 
     */
    protected $filesystem;

    /**
     * Constructor
     * 
     * @param Repository $config
     * @param Filesystem $filesystem
     */
    public function __construct(Repository $config, Filesystem $filesystem)
    {
        $this->config     = $config->get('antares/updater::sandbox.database');
        $builder          = new DatabaseBuilder();
        $default          = config('database.default');
        $this->dbConfig   = config("database.connections.{$default}");
        $this->database   = $builder->getDatabase($this->dbConfig);
        $this->filesystem = $filesystem;
    }

    /**
     * create copy of current database instance
     * 
     * @return boolean
     */
    public function copy()
    {
        try {
            $this->dump()->create()->write();
            return true;
        } catch (Exception $e) {
            Log::emergency($e);
            $this->rollback();
            $this->note($e->getMessage());
            $this->hasError = true;
            return false;
        }
    }

    /**
     * dumping current database instance into file
     * 
     * @return \Antares\Updater\Strategy\Sandbox\Database
     */
    protected function dump()
    {
        $path = array_get($this->config, 'dumps_path');
        if (!$this->filesystem->exists($path)) {
            $this->filesystem->mkdir($path);
        }
        $this->file = $path . DIRECTORY_SEPARATOR . str_random() . '.sql';
        $this->database->dump($this->file);
        return $this;
    }

    /**
     * create new database instance
     * 
     * @return \Antares\Updater\Strategy\Sandbox\Database
     */
    protected function create()
    {
        $command = sprintf('CREATE DATABASE IF NOT EXISTS %s CHARACTER SET %s COLLATE %s', $this->databaseName(), $this->config['character_set'], $this->config['collation']);
        DB::unprepared($command);
        return $this;
    }

    /**
     * create database instance when not exists
     * 
     * @return \Antares\Updater\Strategy\Sandbox\Database
     */
    protected function write()
    {
        $username    = array_get($this->dbConfig, 'username');
        $commandPath = config('laravel-backup.mysql.dump_command_path');

        $process = new Process("find / -type s -name 'mysql.sock'");
        $process->run();
        while ($process->isRunning()) {
            
        }

        $protocol    = '--protocol=socket -S ' . str_replace(PHP_EOL, '', $process->getOutput());
        $commandCopy = sprintf("%smysqldump %s -u %s %s | %smysql %s -u %s %s", $commandPath, $protocol, $username, array_get($this->dbConfig, 'database'), $commandPath, $protocol, $username, $this->databaseName());
        $process     = new Process($commandCopy);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new Exception($process->getErrorOutput());
        }
        return $this;
    }

    /**
     * drop created database
     * 
     * @return \Antares\Updater\Strategy\Sandbox\Database
     */
    public function rollback($version = null)
    {
        try {
            $this->setVersion($version);
            DB::unprepared(sprintf('DROP DATABASE %s', $this->databaseName()));
        } catch (Exception $e) {
            Log::emergency($e);
            $this->note($e->getMessage());
        }
        return $this;
    }

}
