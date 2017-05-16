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






namespace Antares\Updater\BackupHandlers\Database;

/**
 * Based on https://github.com/spatie/laravel-backup
 * 
 * @author Spatie
 * @modifier Łukasz Cirut
 */
use Antares\Updater\Console;
use Exception;

class DatabaseBuilder
{

    protected $database;
    protected $console;

    public function __construct()
    {
        $this->console = new Console();
    }

    public function getDatabase(array $realConfig)
    {
        switch ($realConfig['driver']) {
            case 'mysql':
                try {
                    $this->buildMySQL($realConfig);
                } catch (Exception $e) {
                    throw new \Exception('Whoops, ' . $e->getMessage());
                }
                break;

            case 'pgsql':
                try {
                    $this->buildPgSql($realConfig);
                } catch (Exception $e) {
                    throw new \Exception('Whoops, ' . $e->getMessage());
                }
                break;
        }

        return $this->database;
    }

    protected function buildMySQL(array $config)
    {
        $port = isset($config['port']) ? $config['port'] : 3306;

        $socket = isset($config['unix_socket']) ? $config['unix_socket'] : '';

        $this->database = new Databases\MySQLDatabase(
                $this->console, $config['database'], $config['username'], $config['password'], $this->determineHost($config), $port, $socket
        );
    }

    /**
     * Build a PgSQLDatabase instance.
     *
     * @param array $config
     */
    protected function buildPgSql(array $config)
    {
        $port = isset($config['port']) ? $config['port'] : 5432;

        $schema = isset($config['schema']) ? $config['schema'] : 'public';

        $this->database = new Databases\PgSQLDatabase(
                $this->console, $config['database'], $schema, $config['username'], $config['password'], $this->determineHost($config), $port
        );
    }

    /**
     * Determine the host from the given config.
     *
     * @param array $config
     *
     * @return string
     *
     * @throws Exception
     */
    public function determineHost(array $config)
    {
        if (isset($config['host'])) {
            return $config['host'];
        }

        if (isset($config['read']['host'])) {
            return $config['read']['host'];
        }

        throw new Exception('could not determine host from config');
    }

}
