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



namespace Antares\Logger\Http\Controllers\Admin;

use Antares\Datatables\Html\Builder;
use Antares\Logger\Contracts\IndexListener;
use Antares\Logger\Processor\IndexProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use function app;
use function handles;
use function trans;

class IndexController extends AdminController implements IndexListener
{

    /**
     * implments instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware("antares.can:antares/logger::view-logs", ['only' => ['index', 'system']]);
        $this->middleware("antares.can:antares/logger::error-details", ['only' => ['details']]);
        $this->middleware("antares.can:antares/logger::error-delete", ['only' => ['delete']]);
        $this->middleware("antares.can:antares/logger::error-download", ['only' => ['download']]);
    }

    /**
     * list of system logs
     * 
     * @return View
     */
    public function system(Request $request, Builder $htmlBuilder)
    {
        return $this->processor->system($request, $htmlBuilder);
    }

    /**
     * details of log in date
     * 
     * @param String $date
     * @param String $level
     * @return View
     */
    public function details($date, $level = null)
    {
        return $this->processor->details($date, $level);
    }

    /**
     * Delete error log
     * 
     * @param String $date
     * @return RedirectResponse
     */
    public function delete($date)
    {
        return $this->processor->delete($date, $this);
    }

    /**
     * when delete error log failed
     * 
     * @return RedirectResponse
     */
    public function deleteFailed()
    {
        $message = trans('Error log has not been deleted.');
        app('antares.messages')->add('error', $message);
        return $this->redirect(handles('antares::logger/system/index'));
    }

    /**
     * when delete error log completed successfully
     * 
     * @return RedirectResponse
     */
    public function deleteSuccess()
    {
        $message = trans('Error log has been deleted.');
        app('antares.messages')->add('success', $message);
        return $this->redirect(handles('antares::logger/system/index'));
    }

    /**
     * when download error log
     * 
     * @param String $date
     * @return mixed
     */
    public function download($date)
    {
        return $this->processor->download($date);
    }

}
