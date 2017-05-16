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
use Antares\Updater\Processor\ProductionProcessor as Processor;
use Antares\Updater\Contracts\ProductionListener;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductionController extends AdminController implements ProductionListener
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
        $this->middleware('antares.can:antares/updater::production-set', ['only' => ['iterations', 'validate', 'backup', 'migration', 'finish', 'rollback', 'installed'],]);
    }

    /**
     * create list of iterations for migration from sandbox to production
     */
    public function iterations()
    {
        return $this->processor->iterations($this);
    }

    /**
     * validate whether sandbox can be set as production
     */
    public function validate()
    {
        return $this->processor->validate($this);
    }

    /**
     * backup production application
     */
    public function backup()
    {
        return $this->processor->backup($this);
    }

    /**
     * migrate all files from sandbox instance
     */
    public function migration()
    {
        return $this->processor->migration($this);
    }

    /**
     * finish migration process
     */
    public function finish()
    {
        return $this->processor->finish($this);
    }

    /**
     * rolling back production update
     */
    public function rollback()
    {
        return $this->processor->rollback($this);
    }

    /**
     * response when every iteration of production update has completed successfully
     * 
     * @param mixed $data
     * @return \Illuminate\View\View
     */
    public function success($data = null)
    {
        if ($this->request->ajax()) {
            return new JsonResponse($data);
        }
        return view('antares/updater::admin.production.success', ['data' => $data]);
    }

    /**
     * response when production has been not updated
     * 
     * @param mixed $data
     * @return \Illuminate\View\View
     */
    public function failed($data)
    {
        if ($this->request->ajax()) {
            return new JsonResponse($data, 500);
        }
        return view('antares/updater::admin.production.failed', ['data' => $data]);
    }

    /**
     * response when production has been updated successfully
     */
    public function installed()
    {
        return view('antares/updater::admin.production.installed');
    }

}
