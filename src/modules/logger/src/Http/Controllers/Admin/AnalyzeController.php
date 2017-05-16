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

use Antares\Logger\Processor\AnalyzeProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Logger\Contracts\AnalyzeListener;
use Illuminate\Http\Request;

class AnalyzeController extends AdminController implements AnalyzeListener
{

    /**
     * implments instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor, Request $request)
    {
        parent::__construct();
        $this->processor = $processor;
        if (!$request->ajax()) {
            return abort(403, 'Method not allowed.');
        }
    }

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware("antares.can:antares/logger::analyzer-dashboard", ['only' => ['index']]);
        $this->middleware('antares.can:antares/logger::analyzer-run', ['only' => ['run']]);
        $this->middleware('antares.can:antares/logger::analyzer-server', ['only' => ['server']]);
        $this->middleware('antares.can:antares/logger::analyzer-system', ['only' => ['system']]);
        $this->middleware('antares.can:antares/logger::analyzer-modules', ['only' => ['modules']]);
        $this->middleware('antares.can:antares/logger::analyzer-version', ['only' => ['version']]);
        $this->middleware('antares.can:antares/logger::analyzer-database', ['only' => ['database']]);
        $this->middleware('antares.can:antares/logger::analyzer-logs', ['only' => ['logs']]);
        $this->middleware('antares.can:antares/logger::analyzer-components', ['only' => ['components']]);
        $this->middleware('antares.can:antares/logger::analyzer-checksum', ['only' => ['checksum']]);
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
     * run analyze actions as task list
     */
    public function run($action)
    {
        if (!method_exists($this, $action)) {
            return abort(500, 'Invalid task name.');
        }
        return $this->{$action}();
    }

    /**
     * get php version
     */
    public function server()
    {
        return $this->processor->server();
    }

    /**
     * read system evnironment
     */
    public function system()
    {
        return $this->processor->system();
    }

    /**
     * read modules list 
     */
    public function modules()
    {
        return $this->processor->modules();
    }

    /**
     * get system version
     */
    public function version()
    {
        return $this->processor->version();
    }

    /**
     * report database tables 
     */
    public function database()
    {
        return $this->processor->database();
    }

    /**
     * get informations about logs
     */
    public function logs()
    {
        return $this->processor->logs();
    }

    /**
     * report installed components and modules
     */
    public function components()
    {
        return $this->processor->components();
    }

    /**
     * checksum of system files report
     */
    public function checksum()
    {
        return $this->processor->checksum();
    }

}
