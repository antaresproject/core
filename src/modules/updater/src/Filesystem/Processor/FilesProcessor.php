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






namespace Antares\Updater\Filesystem\Processor;

use Antares\Updater\Contracts\FilesProcessor as FilesProcessorContract;
use Antares\Updater\Traits\Note as NoteTrait;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Exception;

class FilesProcessor implements FilesProcessorContract
{

    use NoteTrait;

    /**
     * filesystem container
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * list of files from update package
     *
     * @var array 
     */
    protected $migrationList = [];

    /**
     * constructing
     * 
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * process files to new repository version 
     * 
     * @param String $path
     */
    public function process($path)
    {
        try {
            $this->note('Preparing to migrate update files...');
            $files = $this->files->allFiles($path);
            foreach ($files as $file) {
                $relativePath = $file->getRelativePath();
                $filename     = $file->getFilename();
                if (strlen($relativePath) == 0) {
                    continue;
                }

                $target = base_path($relativePath) . DIRECTORY_SEPARATOR . $filename;
                $dir    = dirname($target);
                if (!is_dir($dir)) {
                    $this->note(sprintf('Creating directory %s ...', $relativePath));
                    $this->files->makeDirectory($dir, 0755, true);
                }
                $this->note(sprintf('Copying file %s ...', $relativePath . DIRECTORY_SEPARATOR . $filename));
                $this->files->copy($file->getRealPath(), $target);
                array_push($this->migrationList, $target);
            }
            $this->note('All update files has been migrated.');
            return true;
        } catch (Exception $e) {
            Log::emergency($e);
            $this->note(sprintf('Exception throws while copying files: [%s] %s', $e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * migration list getter
     * 
     * @return array
     */
    public function getMigrationList()
    {
        return $this->migrationList;
    }

}
