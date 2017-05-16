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






namespace Antares\Updater\Console;

use Antares\Updater\BackupHandlers\Database\DatabaseBackupHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Antares\View\Console\Command;
use Illuminate\Bus\Queueable;
use ZipArchive;
use Exception;

class DbBackupCommand extends Command implements ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        Queueable;

    /**
     * human readable command name
     *
     * @var String
     */
    protected $title = 'Auto Backup Command';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'backup:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database backup';

    /**
     * when command should be executed
     *
     * @var String
     */
    protected $launched = 'daily';

    /**
     * when command can be executed
     *
     * @var array
     */
    protected $availableLaunches = [
        'daily'
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $databaseBackupHandler = app(DatabaseBackupHandler::class);
        $filesToBeBackedUp     = $databaseBackupHandler->getFilesToBeBackedUp();
        if (count($filesToBeBackedUp) != 1) {
            throw new Exception('could not backup db');
        }
        echo 'Database dumped';

        $dbDumpFile    = $filesToBeBackedUp[0];
        $files[]       = ['realFile' => $dbDumpFile, 'fileInZip' => 'dump.sql'];
        $backupZipFile = $this->createZip($files);

        if (filesize($backupZipFile) == 0) {
            $this->warn('The zipfile that will be backupped has a filesize of zero.');
        }
        $destination = $this->resolveDestination();
        foreach ($destination as $fileSystem) {
            $this->copyFileToFileSystem($backupZipFile, $fileSystem);
        }
    }

    /**
     * Copy the given file to given filesystem.
     *
     * @param string $file
     * @param $fileSystem
     */
    protected function copyFileToFileSystem($file, $fileSystem)
    {
        echo 'Start uploading backup to ' . $fileSystem . '-filesystem...';
        $disk           = Storage::disk($fileSystem);
        $backupFilename = $this->getBackupDestinationFileName();
        $this->copyFile($file, $disk, $backupFilename, $fileSystem == 'local');
        echo 'Backup stored on ' . $fileSystem . '-filesystem in file "' . $backupFilename . '"';
    }

    /**
     * Write an ignore-file on the given disk in the given directory.
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $disk
     * @param string                                      $dumpDirectory
     */
    protected function writeIgnoreFile($disk, $dumpDirectory)
    {
        $gitIgnoreContents = '*' . PHP_EOL . '!.gitignore';
        $disk->put($dumpDirectory . '/.gitignore', $gitIgnoreContents);
    }

    /**
     * Determine the name of the zip that contains the backup.
     *
     * @return string
     */
    protected function getBackupDestinationFileName()
    {
        $config          = config('antares/updater::destination');
        $backupDirectory = array_get($config, 'path');
        $prefix          = array_get($config, 'prefix', '');
        $suffix          = array_get($config, 'suffix', '');

        $backupFilename = $prefix . date('Y_m_d_H_i_s') . $suffix . '.zip';
        $destination    = $backupDirectory;
        if ($destination != '') {
            $destination .= '/';
        }
        $destination .= $backupFilename;
        return $destination;
    }

    /**
     * Copy the given file on the given disk to the given destination.
     *
     * @param string                                      $file
     * @param \Illuminate\Contracts\Filesystem\Filesystem $disk
     * @param string                                      $destination
     * @param bool                                        $addIgnoreFile
     */
    protected function copyFile($file, $disk, $destination, $addIgnoreFile = false)
    {
        $destinationDirectory = dirname($destination);

        if ($destinationDirectory != '.') {
            $disk->makeDirectory($destinationDirectory);
        }
        if ($addIgnoreFile) {
            $this->writeIgnoreFile($disk, $destinationDirectory);
        }
        $disk->getDriver()->writeStream($destination, fopen($file, 'r+'));
    }

    /**
     * resolve filesystem to store database dump file
     * 
     * @return array
     */
    protected function resolveDestination()
    {
        $destination = config('antares/updater::destination.filesystem');
        return is_array($destination) ? $destination : [$destination];
    }

    /**
     * Create a zip for the given files.
     *
     * @param $files
     *
     * @return string
     */
    protected function createZip($files)
    {
        echo 'Start zipping ' . count($files) . ' files...';
        $tempZipFile = tempnam(sys_get_temp_dir(), 'laravel-backup-zip');
        $zip         = new ZipArchive();
        $zip->open($tempZipFile, ZipArchive::CREATE);
        foreach ($files as $file) {
            if (file_exists($file['realFile'])) {
                $zip->addFile($file['realFile'], $file['fileInZip']);
            }
        }
        $zip->close();

        echo 'Zip created!';
        return $tempZipFile;
    }

}
