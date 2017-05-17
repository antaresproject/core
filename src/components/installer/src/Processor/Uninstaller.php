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

namespace Antares\Installation\Processor;

use Illuminate\Database\Connection;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Log\Writer as Logger;
use Antares\Installation\Contracts\UninstallListener;
use Exception;
use File;

class Uninstaller {

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Uninstaller constructor.
     * 
     * @param Connection $connection
     * @param Kernel $kernel
     * @param Logger $logger
     */
    public function __construct(Connection $connection, Kernel $kernel, Logger $logger) {
        $this->connection   = $connection;
        $this->kernel       = $kernel;
        $this->logger       = $logger;
    }

    /**
     * Flush cache and session.
     * 
     * @param UninstallListener $listener
     */
    public function flushCacheAndSession(UninstallListener $listener) {
        try {
            $this->kernel->call('cache:clear');

            File::cleanDirectory( storage_path('framework/sessions') );
            File::cleanDirectory( storage_path('logs') );
            File::delete( storage_path('installation.txt') );
            File::delete( storage_path('installation-config.txt') );

            $listener->uninstallSuccess('Cache and session have been flushed successfully.');
        }
        catch(Exception $e) {
            $this->logger->emergency($e->getMessage());
            $listener->uninstallFailed($e->getMessage());
        }

    }

    /**
     * Truncate all tables from the application database.
     * 
     * @param UninstallListener $listener
     * @throws Exception
     */
    public function truncateTables(UninstallListener $listener) {
        $this->connection->beginTransaction();
        $this->connection->statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            $tableNames = $this->connection->getDoctrineSchemaManager()->listTableNames();
            $viewNames  = $this->connection->getDoctrineSchemaManager()->listViews();
            $viewNames  = array_keys($viewNames);

            if( count($tableNames) ) {
                $this->connection->statement('DROP TABLE IF EXISTS ' . implode(', ', $tableNames) );
            }

            if( count($viewNames) ) {
                $this->connection->statement('DROP VIEW IF EXISTS ' . implode(', ', $viewNames) );
            }

            $this->connection->statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->connection->commit();

            $listener->uninstallSuccess('Database tables and views have been removed successfully.');
        }
        catch(Exception $e) {
            $this->connection->rollBack();
            $this->logger->emergency($e->getMessage());
            $listener->uninstallFailed($e->getMessage());
        }
    }
    
}
