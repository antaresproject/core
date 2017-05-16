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

use Illuminate\Database\Eloquent\Model as Eloquent;
use Antares\Updater\Contracts\DatabaseRestorator as SupportDatabaseRestorator;
use Antares\Updater\Contracts\FilesRestorator;
use Antares\Updater\Contracts\BackupStrategy;
use Antares\Updater\Contracts\Decompressor;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Antares\Updater\Traits\Note;
use Exception;

class Backup implements BackupStrategy
{

    use Note;

    /**
     * decompressor instance
     *
     * @var Decompressor 
     */
    protected $decompressor;

    /**
     * filesystem instance
     *
     * @var Filesystem 
     */
    protected $filesystem;

    /**
     * decompressed backup path
     *
     * @var String
     */
    private $decompressedPath;

    /**
     * database restorator instance
     *
     * @var SupportDatabaseRestorator
     */
    private $databaseRestorator;

    /**
     * files restorator instance
     *
     * @var FilesRestorator
     */
    private $filesRestorator;

    /**
     * constructing
     * 
     * @param Decompressor $decompressor
     * @param Filesystem $filesystem
     */
    public function __construct(Decompressor $decompressor, Filesystem $filesystem, SupportDatabaseRestorator $databaseRestorator, FilesRestorator $filesRestorator)
    {

        $this->decompressor       = $decompressor;
        $this->filesystem         = $filesystem;
        $this->databaseRestorator = $databaseRestorator;
        $this->filesRestorator    = $filesRestorator;
    }

    /**
     * realize restoring application from backup
     * 
     * @param \Antares\Updater\Strategy\Backup\Eloquent $model
     */
    public function restore(Eloquent $model)
    {
        ini_set('max_execution_time', 3000);
        try {
            $path = base_path($model->path);
            if (!$this->filesystem->exists($path)) {
                throw new Exception('Unable to find valid backup path.');
            }
            $this->decompressedPath = $this->decompressor->decompress($path);
            $this->restoreDatabase()->restoreFiles();
        } catch (Exception $ex) {
            Log::emergency($ex);
            $this->hasError = true;
            $this->note($ex->getMessage());
        }
        return $this;
    }

    /**
     * restoring database
     * 
     * @return \Antares\Updater\Strategy\Backup\Backup
     * @throws Exception
     */
    protected function restoreDatabase()
    {
        $this->databaseRestorator
                ->prepare($this->decompressedPath)
                ->create()
                ->copy()
                ->dump()
                ->drop();

        return $this;
    }

    /**
     * restoring files from backup
     * 
     * @return \Antares\Updater\Strategy\Backup\Backup
     */
    protected function restoreFiles()
    {
        $this->filesRestorator
                ->prepare($this->decompressedPath)
                ->restore();
        return $this;
    }

}
