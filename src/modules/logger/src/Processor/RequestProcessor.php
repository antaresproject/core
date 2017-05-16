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



namespace Antares\Logger\Processor;

use Antares\Logger\Contracts\RequestPresenter as Presenter;
use Antares\Logger\Contracts\RequestListener;
use Antares\Foundation\Processor\Processor;

class RequestProcessor extends Processor
{

    /**
     * constructing
     * 
     * @param Presenter $presenter
     */
    public function __construct(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * default index action
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->presenter->table();
    }

    /**
     * on clear request log
     * 
     * @param String $date
     * @param RequestListener $listener
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear($date, RequestListener $listener)
    {
        app('antares.logger')->setOld(['name' => app('logger.filesystem')->getLogFilename($date)])->keep('high');
        if (app('logger.filesystem')->delete($date)) {
            return $listener->clearSuccess();
        }
        return $listener->clearFailed();
    }

    /**
     * show details about single request log file
     * 
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return $this->presenter->tableRequestLog();
    }

    /**
     * Download request log
     * 
     * @param String $date
     */
    public function download(RequestListener $listener, $date)
    {
        $filename = app('logger.filesystem')->getLogFilename($date);
        $path     = dirname(config('request-logger.logger.file')) . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($path)) {
            return $listener->downloadFailed();
        }
        return response()->download($path);
    }

}
