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

use Antares\Logger\Processor\DownloadProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Logger\Contracts\DownloadListener;

class DownloadController extends AdminController implements DownloadListener
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
        $this->middleware("antares.can:antares/logger::report-download", ['only' => ['download']]);
    }

    /**
     * download default action
     * 
     * @return \Illuminate\View\View
     */
    public function download($path)
    {
        return $this->processor->download($path, $this);
    }

    /**
     * when file to download not exists
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downloadFailed()
    {
        $message = trans('File does not exists.');
        app('antares.messages')->add('error', $message);
        return redirect()->back();
    }

}
