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
use Antares\Updater\Processor\ModuleProcessor as Processor;
use Antares\Updater\Contracts\ModuleListener;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleController extends AdminController implements ModuleListener
{

    /**
     * request instance
     *
     * @var Request 
     */
    protected $request;

    /**
     * implments instance of controller
     * 
     * @param Processor $processor
     * @param Request $request
     */
    public function __construct(Processor $processor, Request $request)
    {
        parent::__construct();
        $this->processor = $processor;
        $this->request   = $request;
    }

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.csrf');
        $this->middleware('antares.can:antares/updater::update-module', ['only' => ['update'],]);
    }

    /**
     * run module update
     */
    public function update($name, $version)
    {
        return $this->processor->update($name, $version, $this);
    }

    /**
     * module update success
     * 
     * @param mixed $data
     * @return \Illuminate\View\View
     */
    public function success()
    {
        $message = trans('Module has been updated successfully');
        if ($this->request->ajax()) {
            return new JsonResponse(['message' => $message]);
        }
        return $this->redirectWithMessage(handles('antares::updater/update'), $message);
    }

    /**
     * module update failed
     * 
     * @param mixed $data
     * @return \Illuminate\View\View
     */
    public function failed($data)
    {
        if ($this->request->ajax()) {
            return new JsonResponse($data, 500);
        }
        return $this->redirectWithMessage(handles('antares::updater/update'), implode('<br />', $data), 'error');
    }

}
