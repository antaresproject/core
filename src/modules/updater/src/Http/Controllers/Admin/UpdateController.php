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

use Antares\Updater\Processor\UpdateProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Updater\Contracts\UpdateListener;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateController extends AdminController implements UpdateListener
{

    /**
     * request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.csrf');
        $this->middleware('antares.can:antares/updater::update-system', ['only' => ['start'],]);
    }

    /**
     * implments instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor, Request $request)
    {
        parent::__construct();
        $this->processor = $processor;
        $this->request   = $request;
    }

    /**
     * installation is starting
     * 
     * @param String $version
     * @return \Illuminate\View\View
     */
    public function start($version)
    {
        return $this->processor->start($this, $version);
    }

    /**
     * when updating is successfull
     * 
     * @param array $data
     * @return \Illuminate\View\View
     */
    public function success($data)
    {
        if ($this->request->ajax()) {
            return new JsonResponse($data);
        }
        return view('antares/updater::admin.update.start', $data);
    }

    /**
     * when updating is failed
     * 
     * @param mixed $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function failed($message)
    {
        if ($this->request->ajax()) {
            return new JsonResponse($message, 500);
        }
        return redirect_with_message(handles('antares::updater/update'), $message, 'error');
    }

}
