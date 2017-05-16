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

use Antares\Updater\Contracts\FilesRestorator as FilesRestoratorContract;
use Illuminate\Filesystem\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;

class FilesRestorator implements FilesRestoratorContract
{

    /**
     * path to backup repository
     *
     * @var String
     */
    protected $path;

    /**
     * filesystem instance
     *
     * @var Filesystem 
     */
    protected $filesystem;

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
     * prepare file restore before run process
     * @param String $path
     * 
     * @return \Antares\Updater\Strategy\Backup\FilesRestorator
     */
    public function prepare($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * copies files from backup to primary application instance
     * 
     * @return \Antares\Updater\Strategy\Backup\FilesRestorator
     */
    public function restore()
    {
        $backupPath = $this->getBackupRepositoryPath();
        if (is_null($backupPath)) {
            return $this;
        }
        $files = $this->filesystem->allFiles($backupPath);
        foreach ($files as $file) {
            $filePath = $file->getPath();

            if (!$this->filesystem->isDirectory($filePath)) {
                $this->filesystem->makeDirectory($filePath, 0755, true);
            }
            $target = base_path($file->getRelativePath() . DIRECTORY_SEPARATOR . $file->getFilename());
            $this->filesystem->copy($file->getRealPath(), $target);
        }
        return $this;
    }

    /**
     * get backup repository path
     * 
     * @return String
     * @throws Exception
     */
    private function getBackupRepositoryPath()
    {
        $projectDirectory = last(explode(DIRECTORY_SEPARATOR, base_path()));

        $dir_iterator = new RecursiveDirectoryIterator($this->path);
        $iterator     = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

        $backupPath = null;
        foreach ($iterator as $file) {
            if ($file->isDir() && $file->getFilename() == $projectDirectory) {
                $backupPath = $file->getRealPath();
                break;
            }
        }

        return $backupPath;
    }

}
