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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Http\Controllers\Admin;

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Updater\Processor\IndexProcessor as Processor;
use Illuminate\Http\Request;

class IndexController extends AdminController
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
        $this->middleware('antares.can:antares/updater::versions-list', ['only' => ['index'],]);
        $this->middleware('antares.can:antares/updater::hide-version-alert', ['only' => ['hide'],]);
        $this->middleware('antares.can:antares/updater::updater-dashboard', ['only' => ['update'],]);
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
     * hide version alert
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function hide(Request $request)
    {
        if (!$request->ajax()) {
            return redirect()->back();
        }
        return $this->processor->hide();
    }

    /**
     * page with update system
     * 
     * @return \Illuminate\View\View
     */
    public function update()
    {
        return $this->processor->update();
    }

    /**
     * sample error page
     * 
     * @throws \Exception
     */
    public function error()
    {
        throw new \Exception('Sample error');
    }

}
