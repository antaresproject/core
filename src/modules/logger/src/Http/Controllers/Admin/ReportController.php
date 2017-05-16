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

use Antares\Logger\Processor\ReportProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Logger\Contracts\ReportListener;
use Illuminate\Http\JsonResponse;

class ReportController extends AdminController implements ReportListener
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
        $this->middleware("antares.can:antares/logger::report-send", ['only' => ['send']]);
    }

    /**
     * index default action
     * 
     * @return \Illuminate\View\View
     */
    public function send()
    {
        return $this->processor->send($this);
    }

    /**
     * when delete error log failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendFailed()
    {
        $message = trans('Report has not been sent.');
        app('antares.messages')->add('error', $message);
        if (app('request')->ajax()) {
            return new JsonResponse(['message' => $message], 500);
        }
        return redirect(handles('antares::/'));
    }

    /**
     * when delete error log completed successfully
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendSuccess()
    {
        $message = trans('Report has been send.');
        app('antares.messages')->add('success', $message);
        if (app('request')->ajax()) {
            return new JsonResponse(['message' => $message], 200);
        }
        return redirect(handles('antares::/'));
    }

    /**
     * when generates new system report
     * 
     * @return JsonResponse
     */
    public function generate()
    {
        return $this->processor->generate();
    }

}
