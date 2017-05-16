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

namespace Antares\Logger\Adapter;

use Antares\Logger\Entities\RequestLogCollection;
use Illuminate\Database\Eloquent\Collection;
use Arcanedev\LogViewer\Contracts\LogViewer;
use Antares\Logger\Model\Logs;

class LogsAdapter
{

    /**
     * verify application files checksum
     * 
     * @return array 
     */
    public function verify()
    {
        $collection          = new RequestLogCollection();
        $requests            = $this->requestLogs($collection);
        $errorLogsCollection = app(LogViewer::class)->statsTable();
        $errorLogs           = $this->errorLogs($errorLogsCollection->rows());

        $activeLogsCollection = Logs::with('component', 'priority', 'user', 'brand')->whereHas('component', function($q) {
                    $q->where('active', 1);
                })->where('brand_id', brand_id())->get();

        $activeLogs = $this->activeLogs($activeLogsCollection);

        $return = ['requests' => $requests, 'errors' => $errorLogs, 'activity' => $activeLogs];
        $this->validate($return);
        return $return;
    }

    /**
     * validate logs verification
     * 
     * @param array $data
     * @return array
     */
    protected function validate(array &$data)
    {
        $messages = [];
        if (!empty($data['errors']) && !empty($data['activity']) && !empty($data['requests'])) {
            array_push($messages, ['warning', trans('It is recommended to periodically clean system logs.')]);
        }
        $data = array_merge($data, ['messages' => $messages]);
        return $data;
    }

    /**
     * get active logs summary
     * 
     * @param Collection $collection
     * @return array
     */
    protected function activeLogs(Collection $collection)
    {
        $return = [];
        foreach ($collection as $model) {
            $module = $model->component->name;
            if (!isset($return[$module][$model->name])) {
                $return[$module][$model->name] = 0;
            }
            $return[$module][$model->name] += 1;
        }
        return $return;
    }

    /**
     * get error logs summary
     * 
     * @param array $collection
     * @return array
     */
    protected function errorLogs(array $collection)
    {
        $return = [
            'total_logs_count' => count($collection)
        ];
        $item   = [];
        foreach ($collection as $row) {
            $row  = array_except($row, 'date');
            $keys = array_keys($row);
            if (empty($item)) {
                $item = array_fill_keys($keys, 0);
            }
            foreach ($keys as $key) {
                $item[$key] += $row[$key];
            }
        }
        return $return + $item;
    }

    /**
     * get request logs summary
     * 
     * @param RequestLogCollection $collection
     * @return array
     */
    protected function requestLogs(RequestLogCollection $collection)
    {
        $requests        = [
            'total' => $collection->count(),
        ];
        $size            = 0;
        $requestsCount   = 0;
        $lastCreatedDate = 0;
        $lastUpdatedDate = 0;
        foreach ($collection as $model) {
            $size          += $model->file()->getSize();
            $requestsCount += $model->entries()->count();
            $createdAt     = $model->createdAt()->toDateTimeString();
            if ($lastCreatedDate < $createdAt) {
                $lastCreatedDate = $createdAt;
            }
            $updatedAt = $model->updatedAt()->toDateTimeString();
            if ($lastUpdatedDate < $updatedAt) {
                $lastUpdatedDate = $updatedAt;
            }
        }
        return $requests += [
            'size'              => $size,
            'requests_count'    => $requestsCount,
            'last_created_date' => $lastCreatedDate,
            'last_updated_date' => $lastUpdatedDate
        ];
    }

}
