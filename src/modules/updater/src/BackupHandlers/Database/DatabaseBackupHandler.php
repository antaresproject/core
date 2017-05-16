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
use Exception;
use Antares\Updater\BackupHandlers\BackupHandlerInterface;

class DatabaseBackupHandler implements BackupHandlerInterface
{

    protected $databaseBuilder;

    public function __construct(DatabaseBuilder $databaseBuilder)
    {
        $this->databaseBuilder = $databaseBuilder;
    }

    /**
     * Get database configuration.
     *
     * @param string $connectionName
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getDatabase($connectionName = '')
    {
        $connectionName = $connectionName ?: config('database.default');
        $dbDriver       = config("database.connections.{$connectionName}.driver");

        if ($dbDriver != 'mysql' && $dbDriver != 'pgsql') {
            throw new Exception('laravel-backup can only backup mysql / pgsql databases');
        }

        return $this->databaseBuilder->getDatabase(config("database.connections.{$connectionName}"));
    }

    public function getDumpedDatabase()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'laravel-backup-db');

        $success = $this->getDatabase()->dump($tempFile);
        if (!$success || filesize($tempFile) == 0) {
            throw new Exception('Could not create backup of db');
        }

        return $tempFile;
    }

    /**
     * Returns an array of files which should be backed up.
     *
     * @return array
     */
    public function getFilesToBeBackedUp()
    {
        return [$this->getDumpedDatabase()];
    }

}
