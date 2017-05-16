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

use Illuminate\Pagination\LengthAwarePaginator;
use Antares\Logger\Support\Collection;
use Exception;

class RequestLogCollection extends Collection
{
    /* ------------------------------------------------------------------------------------------------
      |  Properties
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /* ------------------------------------------------------------------------------------------------
      |  Constructor
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Constructor
     *
     * @param  array  $items
     */
    public function __construct($items = [])
    {
        $this->setFilesystem(app('logger.filesystem'));
        parent::__construct($items);
        if (empty($items)) {
            $this->load();
        }
    }

    /* ------------------------------------------------------------------------------------------------
      |  Getters & Setters
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Set the filesystem instance.
     *
     * @param  FilesystemInterface  $filesystem
     *
     * @return self
     */
    private function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /* ------------------------------------------------------------------------------------------------
      |  Main functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Load all logs.
     *
     * @return self
     */
    private function load()
    {
        foreach ($this->filesystem->dates(true) as $date => $path) {
            $raw = $this->filesystem->read($date);
            $this->put($date, RequestLog::make($date, $path, $raw));
        }

        return $this;
    }

    /**
     * Get a log.
     *
     * @param  string     $date
     * @param  mixed|null $default
     *
     * @return Log
     *
     * @throws LogNotFound
     */
    public function get($date, $default = null)
    {
        if (!$this->has($date)) {
            throw new Exception('Log not found in this date [' . $date . ']');
        }

        return parent::get($date, $default);
    }

    /**
     * Paginate logs.
     *
     * @param  int  $perPage
     *
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = 30)
    {
        $page      = request()->input('page', 1);
        $items     = $this->slice(($page * $perPage) - $perPage, $perPage, true);
        $paginator = new LengthAwarePaginator($items, $this->count(), $perPage, $page);

        $paginator->setPath(request()->url());

        return $paginator;
    }

    /**
     * Get a log. (alias)
     * @see LogCollection::get()
     *
     * @param  string  $date
     *
     * @return Log
     *
     * @throws LogNotFound
     */
    public function log($date)
    {
        return $this->get($date);
    }

    /**
     * Get log entries.
     *
     * @param  string  $date
     * @param  string  $level
     *
     * @return LogEntryCollection|null
     */
    public function entries($date, $level = 'all')
    {
        return $this->get($date)->entries($level);
    }

    /**
     * Get logs statistics
     *
     * @return array
     */
    public function stats()
    {
        $stats = [];

        foreach ($this->items as $date => $log) {
            /** @var Log $log */
            $stats[$date] = $log->stats();
        }

        return $stats;
    }

    /**
     * List the log files (dates).
     *
     * @return array
     */
    public function dates()
    {
        return $this->keys()->toArray();
    }

    /**
     * Get entries total.
     *
     * @param  string  $level
     *
     * @return int
     */
    public function total($level = 'all')
    {
        return (int) $this->sum(function (Log $log) use ($level) {
                    return $log->entries($level)->count();
                });
    }

    /**
     * Get logs tree.
     *
     * @param  bool|false  $trans
     *
     * @return array
     */
    public function tree($trans = false)
    {
        $tree = [];

        foreach ($this->items as $date => $log) {
            /** @var Log $log */
            $tree[$date] = $log->tree($trans);
        }

        return $tree;
    }

    /**
     * Get logs menu.
     *
     * @param  bool|true  $trans
     *
     * @return array
     */
    public function menu($trans = true)
    {
        $menu = [];

        foreach ($this->items as $date => $log) {
            /** @var Log $log */
            $menu[$date] = $log->menu($trans);
        }

        return $menu;
    }

}
