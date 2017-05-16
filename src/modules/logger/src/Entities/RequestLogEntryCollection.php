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

use Antares\Logger\Utilities\LogParser;
use Antares\Logger\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RequestLogEntryCollection extends Collection
{
    /* ------------------------------------------------------------------------------------------------
      |  Main Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Load raw log entries
     *
     * @param  string  $raw
     *
     * @return self
     */
    public function load($raw)
    {
        foreach (LogParser::parse($raw) as $entry) {
            list($level, $header, $stack) = array_values($entry);
            $this->push(new RequestLogEntry($level, $header, $stack));
        }

        return $this;
    }

    /**
     * Paginate log entries.
     *
     * @param  int  $perPage
     *
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = 20)
    {
        $page      = request()->input('page', 1);
        $items     = $this->slice(($page * $perPage) - $perPage, $perPage, true);
        $paginator = new LengthAwarePaginator($items, $this->count(), $perPage, $page);

        $paginator->setPath(request()->url());

        return $paginator;
    }

    /**
     * Get filtered log entries by level
     *
     * @param  string  $level
     *
     * @return LogEntryCollection
     */
    public function filterByLevel($level)
    {
        return $this->filter(function(LogEntry $entry) use ($level) {
                    return $entry->isSameLevel($level);
                });
    }

    /**
     * Get log entries stats.
     *
     * @return array
     */
    public function stats()
    {
        $counters = $this->initStats();

        foreach ($this->groupBy('level') as $level => $entries) {
            $counters[$level] = $count            = count($entries);
            $counters['all'] += $count;
        }

        return $counters;
    }

    /**
     * Get the log entries navigation tree.
     *
     * @param  bool|false  $trans
     *
     * @return array
     */
    public function tree($trans = false)
    {
        $tree = $this->stats();

        array_walk($tree, function(&$count, $level) use ($trans) {
            $count = [
                'name'  => $trans ? trans("log-viewer::levels.$level") : $level,
                'count' => $count,
            ];
        });

        return $tree;
    }

    /* ------------------------------------------------------------------------------------------------
      |  Other Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Init stats counters.
     *
     * @return array
     */
    private function initStats()
    {
        $levels = array_merge_recursive(
                ['all'], array_keys(log_viewer()->levels(true))
        );

        return array_map(function () {
            return 0;
        }, array_flip($levels));
    }

}
