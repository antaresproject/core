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






namespace Antares\Updater\Strategy\Backup;

use Antares\Updater\Contracts\DatabaseRestorator as DatabaseRestoratorContract;
use Symfony\Component\Process\Process;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Exception;

class DatabaseRestorator implements DatabaseRestoratorContract
{

    /**
     * filesystem instance
     *
     * @var Filesystem 
     */
    protected $filesystem;

    /**
     * is valid flag
     *
     * @var boolean 
     */
    private $isValid = true;

    /**
     * config container
     *
     * @var array 
     */
    private $config = [];

    /**
     * name of temporary database
     *
     * @var String 
     */
    private $tempDatabase;

    /**
     * constructing
     * 
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * prepare data before run backup process
     * 
     * @param type $path
     * @return \Antares\Updater\Strategy\Backup\DatabaseRestorator
     */
    public function prepare($path)
    {
        $files = $this->filesystem->glob($path . DIRECTORY_SEPARATOR . 'dump.sql');
        if (empty($files)) {
            $this->isValid = false;
            return $this;
        }
        $database     = config('database');
        $default      = array_get($database, 'default');
        $config       = config('antares/updater::sandbox.database');
        $this->config = [
            'username'         => array_get($database, "connections.{$default}.username"),
            'primary_database' => array_get($database, "connections.{$default}.database"),
            'command_path'     => config('laravel-backup.mysql.dump_command_path'),
            'character_set'    => array_get($config, 'character_set'),
            'collation'        => array_get($config, 'collation'),
            'files'            => $files
        ];
        return $this;
    }

    /**
     * create database temporary instance
     * 
     * @return \Antares\Updater\Strategy\Backup\DatabaseRestorator
     */
    public function create()
    {
        if (!$this->isValid) {
            return $this;
        }
        $this->tempDatabase = 'temp_' . time();
        $command            = sprintf('CREATE DATABASE IF NOT EXISTS %s CHARACTER SET %s COLLATE %s', $this->tempDatabase, array_get($this->config, 'character_set'), array_get($this->config, 'collation'));
        DB::unprepared($command);
        return $this;
    }

    /**
     * copy database instance from backup to primary database instance
     * 
     * @return \Antares\Updater\Strategy\Backup\DatabaseRestorator
     * @throws Exception
     */
    public function copy()
    {
        if (!$this->isValid) {
            return $this;
        }
        $files    = array_get($this->config, 'files');
        $protocol = $this->getProtocol();
        foreach ($files as $file) {
            $command = sprintf("%smysql %s -u %s %s < %s", array_get($this->config, 'command_path'), $protocol, array_get($this->config, 'username'), $this->tempDatabase, $file);
            $process = new Process($command);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new Exception("Unable to create database copy.");
            }
        }
        return $this;
    }

    /**
     * Gets socket protocol path
     * 
     * @return String
     */
    protected function getProtocol()
    {
        $socketFinder = new Process("find / -type s -name 'mysql.sock'");
        $socketFinder->run();
        while ($socketFinder->isRunning()) {
            
        }
        return '--protocol=socket -S ' . str_replace(PHP_EOL, '', $socketFinder->getOutput());
    }

    /**
     * create dump database between backup and primary database instance
     * 
     * @return \Antares\Updater\Strategy\Backup\DatabaseRestorator
     * @throws Exception
     */
    public function dump()
    {
        if (!$this->isValid) {
            return $this;
        }
        $commandPath = array_get($this->config, 'command_path');
        $username    = array_get($this->config, 'username');
        $protocol    = $this->getProtocol();
        $command     = sprintf("%smysqldump %s -u %s %s | %smysql %s -u %s %s", $commandPath, $protocol, $username, $this->tempDatabase, $commandPath, $protocol, $username, array_get($this->config, 'primary_database'));
        $process     = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new Exception("Unable to copy database from backup.");
        }
        return $this;
    }

    /**
     * drops temporary database instance
     * 
     * @return \Antares\Updater\Strategy\Backup\DatabaseRestorator
     * @throws Exception
     */
    public function drop()
    {
//        if (!$this->isValid) {
//            return $this;
//        }
//        $command = sprintf("%smysqladmin -u %s drop %s", array_get($this->config, 'command_path'), array_get($this->config, 'username'), $this->tempDatabase);
//        $process = new Process($command);
//        $process->run();
//        if (!$process->isSuccessful()) {
//            throw new Exception("Unable to drop temporary backup database.");
//        }
        return $this;
    }

}
