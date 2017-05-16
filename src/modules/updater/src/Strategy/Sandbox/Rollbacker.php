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

use Antares\Updater\Contracts\Rollbacker as RollbackerContract;
use Antares\Updater\Contracts\SandboxFiles as FilesContract;
use Antares\Updater\Contracts\Database as DatabaseContract;
use Illuminate\Support\Facades\Log;
use Exception;

class Rollbacker extends AbstractStrategy implements RollbackerContract
{

    /**
     * files strategy instance
     *
     * @var FilesContract 
     */
    protected $files;

    /**
     * database strategy instance
     *
     * @var DatabaseContract 
     */
    protected $database;

    /**
     * constructing
     * 
     * @param FilesContract $files
     * @param DatabaseContract $database
     */
    public function __construct(FilesContract $files, DatabaseContract $database)
    {
        $this->files    = $files;
        $this->database = $database;
    }

    /**
     * rollback migration process
     */
    public function rollback()
    {
        try {
            $version = $this->getVersion();
            $this->database->rollback($version);
            $this->files->rollback($version);
            return true;
        } catch (Exception $e) {
            Log::emergency($e);
            $this->hasError = true;
            $this->note($e->getMessage());
            return false;
        }
    }

}
