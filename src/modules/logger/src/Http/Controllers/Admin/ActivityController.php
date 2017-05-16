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

use Antares\Logger\Processor\ActivityProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Logger\Contracts\ActivityListener;
use Illuminate\Http\Request;

class ActivityController extends AdminController implements ActivityListener
{

    /**
     * implments instance of controller
     * 
     * @param Processor $processor
     * @param Request $request
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
        $this->middleware("antares.can:antares/logger::activity-dashboard", ['only' => ['index']]);
        $this->middleware("antares.can:antares/logger::activity-delete-log", ['only' => ['delete']]);
        $this->middleware("antares.can:antares/logger::activity-show-details", ['only' => ['show']]);
    }

    /**
     * index default action
     * 
     * @return \Illuminate\View\View
     */
    public function index($type = null)
    {
        return $this->processor->index($type);
    }

    /**
     * Deletes activity log
     * 
     * @param numeric $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id = null)
    {
        return $this->processor->delete($id, $this);
    }

    /**
     * when delete log failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFailed()
    {
        $message = trans('Log has not been deleted.');
        app('antares.messages')->add('error', $message);
        return redirect()->back();
    }

    /**
     * when delete log completed successfully
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSuccess()
    {
        $message = trans('Log has been deleted.');
        app('antares.messages')->add('success', $message);
        return redirect()->back();
    }

    /**
     * show details about single log row
     * 
     * @param numeric $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return $this->processor->show($id);
    }

    /**
     * Download activity logs as csv
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download()
    {
        return $this->processor->download();
    }

}
