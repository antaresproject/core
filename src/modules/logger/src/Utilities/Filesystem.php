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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Utilities;

use Illuminate\Filesystem\Filesystem as IlluminateFilesystem;
use Illuminate\Support\Facades\Log;
use Exception;

class Filesystem
{
    /* ------------------------------------------------------------------------------------------------
      |  Properties
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * The filesystem instance.
     *
     * @var IlluminateFilesystem
     */
    protected $filesystem;

    /**
     * The base storage path.
     *
     * @var string
     */
    protected $storagePath;

    /* ------------------------------------------------------------------------------------------------
      |  Constructor
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Create a new instance.
     *
     * @param  IlluminateFilesystem  $files
     * @param  string                $storagePath
     */
    public function __construct(IlluminateFilesystem $files, $storagePath)
    {
        $this->filesystem  = $files;
        $this->storagePath = $storagePath;
    }

    /* ------------------------------------------------------------------------------------------------
      |  Getters & Setters
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Get the files instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getInstance()
    {
        return $this->filesystem;
    }

    /* ------------------------------------------------------------------------------------------------
      |  Main Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Get all log files.
     *
     * @return array
     */
    public function all()
    {
        return $this->getFiles('*');
    }

    /**
     * Get all valid log files.
     *
     * @return array
     */
    public function logs()
    {
        return $this->getFiles('http-[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]');
    }

    /**
     * List the log files (Only dates).
     *
     * @param  bool|false  $withPaths
     *
     * @return array
     */
    public function dates($withPaths = false)
    {
        $files = array_reverse($this->logs());
        $dates = $this->extractDates($files);

        if ($withPaths) {
            $dates = array_combine($dates, $files); // [date => file]
        }

        return $dates;
    }

    /**
     * Read the log.
     *
     * @param  string  $date
     *
     * @return string
     *
     * @throws Exception
     */
    public function read($date)
    {
        try {
            $path = $this->getLogPath($date);

            return $this->filesystem->get($path);
        } catch (Exception $e) {
            Log::emergency($e);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Delete the log.
     *
     * @param  string  $date
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete($date)
    {
        $path = $this->getLogPath($date);

        // @codeCoverageIgnoreStart
        if (!$this->filesystem->delete($path)) {
            throw new Exception('There was an error deleting the log.');
        }
        // @codeCoverageIgnoreEnd

        return true;
    }

    /**
     * Get the log file path.
     *
     * @param  string  $date
     *
     * @return string
     *
     * @throws FilesystemException
     */
    public function path($date)
    {
        return $this->getLogPath($date);
    }

    /* ------------------------------------------------------------------------------------------------
      |  Other Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Get all files.
     *
     * @param  string $pattern
     * @param  string $extension
     *
     * @return array
     */
    private function getFiles($pattern, $extension = '.log')
    {
        $pattern = $this->storagePath . DS . $pattern . $extension;
        $files   = array_map('realpath', glob($pattern, GLOB_BRACE));

        return array_filter($files);
    }

    /**
     * Get the log file path.
     *
     * @param  string  $date
     *
     * @return string
     *
     * @throws Exception
     */
    private function getLogPath($date)
    {
        $path = "{$this->storagePath}/http-{$date}.log";

        if (!$this->filesystem->exists($path)) {
            throw new Exception(
            'The log(s) could not be located at : ' . $path
            );
        }

        return realpath($path);
    }

    /**
     * Extract dates from files.
     *
     * @param  array  $files
     *
     * @return array
     */
    private function extractDates(array $files)
    {
        return array_map(function ($file) {
            return extract_date(basename($file));
        }, $files);
    }

    /**
     * Gets log filename
     * 
     * @param Sring $date
     * @return String
     */
    public function getLogFilename($date)
    {
        return last(explode(DIRECTORY_SEPARATOR, $this->getLogPath($date)));
    }

}
