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






namespace Antares\Updater\BackupHandlers\Files;

/**
 * Based on https://github.com/spatie/laravel-backup
 * 
 * @author Spatie
 * @modifier Łukasz Cirut
 */
use Symfony\Component\Finder\Finder;
use SplFileInfo;
use File;

class FilesBackupHandler
{

    protected $includedFiles;
    protected $excludedFiles;

    /**
     * Set all files that should be included.
     *
     * @param array $includedFiles
     *
     * @return $this
     */
    public function setIncludedFiles($includedFiles)
    {
        $this->includedFiles = $includedFiles;

        return $this;
    }

    /**
     * Set all files that should be excluded.
     *
     * @param array $excludedFiles
     *
     * @return $this
     */
    public function setExcludedFiles($excludedFiles)
    {
        $this->excludedFiles = $excludedFiles;

        return $this;
    }

    /**
     * Returns an array of files which should be backed up.
     *
     * @return array
     */
    public function getFilesToBeBackedUp()
    {
        $filesToBeIncluded = $this->getAllPathFromFileArray($this->includedFiles);
        $filesToBeExcluded = $this->getAllPathFromFileArray($this->excludedFiles);

        return array_filter($filesToBeIncluded, function ($file) use ($filesToBeExcluded) {
            return !in_array($file, $filesToBeExcluded);
        });
    }

    /**
     * Make a unique array of all filepaths from a given array of files.
     *
     * @param array $fileArray
     *
     * @return array
     */
    public function getAllPathFromFileArray($fileArray)
    {
        $files = [];

        foreach ($fileArray as $file) {
            if (File::isFile($file)) {
                $files[] = new SplFileInfo($file);
            }

            if (File::isDirectory($file)) {
                $files = array_merge($files, $this->getAllFilesFromDirectory($file));
            }
        }

        return array_unique(array_map(function (SplFileInfo $file) {
                    return $file->getPathName();
                }, $files));
    }

    /**
     * Recursively get all the files within a given directory.
     *
     * @param $directory
     *
     * @return array
     */
    protected function getAllFilesFromDirectory($directory)
    {
        $finder = (new Finder())
                ->ignoreDotFiles(false)
                ->ignoreVCS(true)
                ->files()
                ->in($directory);

        return iterator_to_array($finder);
    }

}
