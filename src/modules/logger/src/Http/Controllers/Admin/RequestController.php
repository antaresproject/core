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

use Antares\Logger\Processor\RequestProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Logger\Contracts\RequestListener;

class RequestController extends AdminController implements RequestListener
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
        $this->middleware("antares.can:antares/logger::request-list", ['only' => ['index']]);
        $this->middleware("antares.can:antares/logger::request-clear", ['only' => ['clear']]);
        $this->middleware("antares.can:antares/logger::request-show", ['only' => ['show']]);
    }

    /**
     * index default action
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->index();
    }

    /**
     * clear request log
     * 
     * @param String $date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear($date)
    {
        return $this->processor->clear($date, $this);
    }

    /**
     * when clear request log failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearFailed()
    {
        app('antares.messages')->add('error', trans('antares/logger::messages.request_log_has_not_been_deleted'));
        return redirect()->back();
    }

    /**
     * when clear request log completed successfully
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearSuccess()
    {
        app('antares.messages')->add('success', trans('antares/logger::messages.request_log_has_been_deleted'));
        return redirect()->back();
    }

    /**
     * show details about single request log row
     * 
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return $this->processor->show();
    }

    /**
     * Download request log
     * 
     * @param String $date
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download($date)
    {
        return $this->processor->download($this, $date);
    }

    /**
     * When download request file failed
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadFailed()
    {
        app('antares.messages')->add('error', trans('antares/logger:messages.download_request_file_error.'));
        return redirect()->back();
    }

}
