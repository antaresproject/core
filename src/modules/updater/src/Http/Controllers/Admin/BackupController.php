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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Updater\Http\Controllers\Admin;

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Updater\Processor\BackupProcessor as Processor;
use Antares\Updater\Contracts\BackupListener;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BackupController extends AdminController implements BackupListener
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
        $this->middleware('antares.forms:view-content');

        $this->middleware('antares.can:antares/updater::backups-list', ['only' => ['index'],]);
        $this->middleware('antares.can:antares/updater::restore-backup', ['only' => ['restore'],]);
    }

    /**
     * Creates new backup
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        return $this->processor->create($this);
    }

    /**
     * When backup creation failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createFailed()
    {
        $message = trans('antares/updater::messages.backup_failed');
        return $this->redirectWithMessage(handles('antares::updater/backups'), $message, 'error');
    }

    /**
     * When backup creation completed successfully
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createSuccess()
    {
        $message = trans('antares/updater::messages.backup_success');
        return $this->redirectWithMessage(handles('antares::updater/backups'), $message, 'success');
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
     * restoring application from backup
     */
    public function restore($id)
    {
        return $this->processor->restore($this, $id);
    }

    /**
     * when restore backup completed successfully
     */
    public function restoreSuccess($data = null)
    {
        if ($this->request->ajax()) {
            return new JsonResponse($data);
        }
        $data = (is_null($data)) ? trans('antares/updater::messages.backup_restored_success') : $data;
        return $this->redirectWithMessage(handles('antares::updater/backups'), $data, 'success');
    }

    /**
     * when restore backup failed
     */
    public function restoreFailed($data)
    {
        if ($this->request->ajax()) {
            return new JsonResponse($data, 500);
        }

        return $this->redirectWithMessage(handles('antares::updater/backups'), $data, 'error');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->processor->delete($this, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSuccess()
    {
        return $this->redirectWithMessage(handles('antares::updater/backups'), trans('antares/updater::messages.backup_delete_success'));
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFailed()
    {
        return $this->redirectWithMessage(handles('antares::updater/backups'), trans('antares/updater::messages.backup_delete_failed'), 'error');
    }

}
