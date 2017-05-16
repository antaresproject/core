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

use Antares\Logger\Contracts\IndexPresenter as Presenter;
use Antares\Foundation\Processor\Processor;
use Antares\Logger\Contracts\IndexListener;
use Antares\Logger\Utilities\LogViewer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class IndexProcessor extends Processor
{
    /* ------------------------------------------------------------------------------------------------
      |  Properties
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * log viewer instance
     *
     * @var LogViewer 
     */
    protected $logViewer;

    /**
     * constructing
     * 
     * @param Presenter $presenter
     */
    public function __construct(Presenter $presenter, LogViewer $logViewer)
    {
        $this->presenter = $presenter;
        $this->logViewer = $logViewer;
    }

    /**
     * realize index action version controller
     * 
     * @return View
     */
    public function system()
    {
        return $this->presenter->table();
    }

    /**
     * details of log by date
     * 
     * @param String $date
     * @param String $level
     * @return View
     */
    public function details($date, $level = null)
    {
        $log     = $this->getLogOrFail($date);
        $levels  = $this->logViewer->levelsNames();
        $entries = $log->entries(is_null($level) ? 'all' : $level);
        return $this->presenter->details($log, $levels, $entries, $level);
    }

    /**
     * Get a log or fail
     *
     * @param  string  $date
     *
     * @return Log|null
     */
    private function getLogOrFail($date)
    {
        try {
            return $this->logViewer->get($date);
        } catch (LogNotFound $e) {
            return abort(404, $e->getMessage());
        }
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @return View
     */
    public function view($view)
    {
        return view('log-viewer::' . $view);
    }

    /**
     * on delete error log 
     * 
     * @param String $date
     * @param IndexListener $listener
     * @return RedirectResponse
     */
    public function delete($date, IndexListener $listener)
    {
        try {
            app('antares.logger')->setOld(['name' => $this->logViewer->get($date)->getFilename()])->keep('high');
            $this->logViewer->delete($date);
            return $listener->deleteSuccess();
        } catch (Exception $ex) {
            return $listener->deleteFailed();
        }
    }

    /**
     * on download error log
     * 
     * @param String $date
     * @return mixed
     */
    public function download($date)
    {
        app('antares.logger')->setOld(['name' => $this->logViewer->get($date)->getFilename()])->keep('medium');
        return $this->logViewer->download($date);
    }

}
