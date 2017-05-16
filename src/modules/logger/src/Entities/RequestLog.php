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



namespace Antares\Logger\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Carbon\Carbon;
use SplFileInfo;

class RequestLog implements Arrayable, Jsonable, JsonSerializable
{
    /* ------------------------------------------------------------------------------------------------
      |  Properties
      | ------------------------------------------------------------------------------------------------
     */

    /** @var string */
    public $date;

    /** @var string */
    private $path;

    /** @var LogEntryCollection */
    private $entries;

    /** @var SplFileInfo */
    private $file;

    /** @var string */
    private $raw;

    /* ------------------------------------------------------------------------------------------------
      |  Constructor
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Constructor
     *
     * @param  string  $date
     * @param  string  $path
     * @param  string  $raw
     */
    public function __construct($date, $path, $raw)
    {
        $this->entries = new RequestLogEntryCollection;
        $this->date    = $date;
        $this->path    = $path;
        $this->file    = new SplFileInfo($path);
        $this->raw     = $raw;

        $this->entries->load($raw);
    }

    /* ------------------------------------------------------------------------------------------------
      |  Getters & Setters
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Get log path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get raw log content
     *
     * @return string
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Get file info
     *
     * @return SplFileInfo
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * Get file size
     *
     * @return string
     */
    public function size()
    {
        return $this->formatSize($this->file->getSize());
    }

    /**
     * Get file creation date
     *
     * @return \Carbon\Carbon
     */
    public function createdAt()
    {
        return Carbon::createFromTimestamp(filectime($this->file()->getRealPath()));
    }

    /**
     * Get file modification date
     *
     * @return \Carbon\Carbon
     */
    public function updatedAt()
    {
        return Carbon::createFromTimestamp($this->file()->getMTime());
    }

    /* ------------------------------------------------------------------------------------------------
      |  Main functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Make a log object
     *
     * @param  string  $date
     * @param  string  $path
     * @param  string  $raw
     *
     * @return self
     */
    public static function make($date, $path, $raw)
    {
        return new self($date, $path, $raw);
    }

    /**
     * Get log entries
     *
     * @param  string  $level
     *
     * @return LogEntryCollection
     */
    public function entries($level = 'all')
    {
        if ($level === 'all') {
            return $this->entries;
        }

        return $this->getByLevel($level);
    }

    /**
     * Get filtered log entries by level
     *
     * @param  string  $level
     *
     * @return LogEntryCollection
     */
    public function getByLevel($level)
    {
        return $this->entries->filterByLevel($level);
    }

    /**
     * Get log stats.
     *
     * @return array
     */
    public function stats()
    {
        return $this->entries->stats();
    }

    /**
     * Get the log navigation tree.
     *
     * @param  bool|false  $trans
     *
     * @return array
     */
    public function tree($trans = false)
    {
        return $this->entries->tree($trans);
    }

    /**
     * Get log entries menu.
     *
     * @param  bool|true  $trans
     *
     * @return array
     */
    public function menu($trans = true)
    {
        return log_menu()->make($this, $trans);
    }

    /* ------------------------------------------------------------------------------------------------
      |  Convert Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Get the log as a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'date'    => $this->date,
            'path'    => $this->path,
            'entries' => $this->entries->toArray()
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Serialize the log object to json data
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /* ------------------------------------------------------------------------------------------------
      |  Other functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Format the file size
     *
     * @param  int  $bytes
     * @param  int  $precision
     *
     * @return string
     */
    private function formatSize($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);

        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }

}
