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

use Antares\Logger\Processor\HistoryProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Logger\Contracts\HistoryListener;
use Illuminate\Http\Request;

class HistoryController extends AdminController implements HistoryListener
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
        $this->middleware("antares.can:antares/logger::history-list", ['only' => ['index']]);
        $this->middleware("antares.can:antares/logger::history-show", ['only' => ['show']]);
        $this->middleware("antares.can:antares/logger::history-delete", ['only' => ['delete']]);
    }

    /**
     * index default action
     * 
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return $this->processor->index($request);
    }

    /**
     * report details
     * 
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return $this->processor->show($id);
    }

    /**
     * delete report
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        return $this->processor->delete($id, $this);
    }

    /**
     * when delete report failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFailed()
    {
        $message = trans('Report has not been deleted.');
        app('antares.messages')->add('error', $message);
        return redirect()->back();
    }

    /**
     * when delete report completed successfully
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSuccess()
    {
        $message = trans('Report has been deleted.');
        app('antares.messages')->add('success', $message);
        return redirect()->back();
    }

}
