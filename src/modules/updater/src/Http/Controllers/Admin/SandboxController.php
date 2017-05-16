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

use Antares\Updater\Processor\SandboxProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Updater\Contracts\SandboxListener;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SandboxController extends AdminController implements SandboxListener
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
        $this->middleware('antares.can:antares/updater::sandbox-dashboard', ['only' => ['index'],]);
        $this->middleware('antares.can:antares/updater::sandbox-run', [
            'only' => [
                'requirements',
                'backup',
                'database',
                'migration',
                'ending',
                'save',
                'open',
                'done',
                'rollback',
                'installed'
            ],]
        );
        $this->middleware('antares.can:antares/updater::sandbox-delete', ['only' => ['delete', 'installed'],]);
    }

    /**
     * sandbox default page
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->index();
    }

    /**
     * getting requirements to create new sandbox instance
     */
    public function requirements()
    {
        return $this->processor->requirements($this);
    }

    /**
     * backup application
     */
    public function backup()
    {
        return $this->processor->backup($this);
    }

    /**
     * creating new database instance and migrating entities
     */
    public function database()
    {
        return $this->processor->database($this);
    }

    /**
     * migrate all files from primary system
     */
    public function migration()
    {

        return $this->processor->migration($this);
    }

    /**
     * ending creation sandbox instance
     */
    public function ending()
    {
        return $this->processor->ending($this);
    }

    /**
     * ending creation sandbox instance
     */
    public function save()
    {
        return $this->processor->save($this);
    }

    /**
     * opening sandbox instance
     */
    public function open()
    {
        return $this->processor->open($this);
    }

    /**
     * sandbox creation is done
     */
    public function done()
    {
        return $this->processor->done($this);
    }

    /**
     * rolling back update
     */
    public function rollback()
    {
        return $this->processor->rollback($this);
    }

    /**
     * delete sandbox action
     * 
     * @param numeric $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete($id)
    {
        return $this->processor->delete($this, $id);
    }

    /**
     * action when delete sandbox instance completed successfully
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteSuccessfull()
    {
        $message = trans('Sandbox has been deleted.');
        return $this->redirectWithMessage(handles('antares::updater/sandboxes'), $message);
    }

    /**
     * action when delete sandbox instance failed
     * 
     * @param mixed $errors
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteFailed($errors)
    {
        return $this->redirectWithMessage(handles("antares::updater/sandboxes"), $errors, 'error');
    }

    /**
     * sandbox creation success page
     * 
     * @param mixed $data
     * @return \Illuminate\View\View
     */
    public function success($data = null)
    {
        if ($this->request->ajax()) {
            return new JsonResponse($data);
        }
        return view('antares/updater::admin.sandbox.success', $data);
    }

    /**
     * sandbox creation failed page
     * 
     * @param mixed $data
     * @return \Illuminate\View\View
     */
    public function failed($data)
    {
        if ($this->request->ajax()) {
            return new JsonResponse($data, 500);
        }
        return view('antares/updater::admin.sandbox.failed', $data);
    }

    /**
     * response when sandbox mode with an update has been installed successfully
     */
    public function installed()
    {
        return view('antares/updater::admin.sandbox.installed');
    }

}
