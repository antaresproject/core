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



namespace Antares\Logger\Http\Presenters;

use Antares\Logger\Contracts\AnalyzePresenter as PresenterContract;
use Illuminate\Contracts\Container\Container;

class AnalyzePresenter implements PresenterContract
{

    /**
     * application container
     * 
     * @var Container
     */
    protected $container;

    /**
     * constructing
     * 
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * server environment
     * 
     * @param array $data
     * @return \Illuminate\View\View
     */
    public function server(array $data)
    {
        return $this->view('server', $data);
    }

    /**
     * presents system information
     * 
     * @param mixed $info
     * @return \Illuminate\View\View
     */
    public function system($info)
    {
        return $this->view('system', ['info' => $info]);
    }

    /**
     * read modules list
     * 
     * @param mixed $directories
     * @return \Illuminate\View\View 
     */
    public function modules($directories)
    {
        return $this->view('modules', compact('directories'));
    }

    /**
     * get system version
     * 
     * @param mixed $data
     * @return \Illuminate\View\View
     */
    public function version($data)
    {
        return $this->view('version', $data);
    }

    /**
     * report database tables
     * 
     * @param array $tables
     * @return \Illuminate\View\View 
     */
    public function database(array $tables)
    {
        $views    = [];
        $others   = [];
        $messages = [];
        foreach ($tables as $table) {
            ($table->Comment == 'VIEW') ?
                            array_push($views, [
                                'name' => $table->Name
                            ]) :
                            array_push($others, [
                                'name'        => $table->Name,
                                'rows'        => $table->Rows,
                                'create_time' => $table->Create_time,
                                'collation'   => $table->Collation,
                                'size'        => $this->formatSize($table->Data_length + $table->Index_length)
            ]);
            if ($table->Rows > 1000) {
                array_push($messages, ['warning', sprintf('Table %s has too many data. Clean table historical entities to increase system performance.', $table->Name)]);
            }
        }
        return $this->view('database', ['views' => $views] + ['tables' => $others] + ['messages' => $messages]);
    }

    /**
     * get informations about logs
     * 
     * @param array $logs
     * @return \Illuminate\View\View
     */
    public function logs(array $logs)
    {
        $logs['requests']['size'] = $this->formatSize($logs['requests']['size']);
        return $this->view('logs', $logs);
    }

    /**
     * list of available components and modules list
     * 
     * @param array $data
     * @return \Illuminate\View\View
     */
    public function components(array $data)
    {
        return $this->view('components', $data);
    }

    /**
     * list of changed, added or deleted files
     * 
     * @param array $data
     * @return \Illuminate\View\View
     */
    public function checksum(array $data)
    {
        return $this->view('checksum', $data);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View
     */
    public function view($view, $data = [], $mergeData = [])
    {
        return view('antares/logger::admin.analyze.' . $view, $data, $mergeData);
    }

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
