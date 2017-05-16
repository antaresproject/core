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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Notifications\Http\Controllers\Admin;

use Antares\Notifications\Processor\IndexProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Notifications\Contracts\IndexListener;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;

class IndexController extends AdminController implements IndexListener
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
        $this->middleware("antares.can:antares/notifications::notifications-details", ['only' => ['show']]);
        $this->middleware("antares.can:antares/notifications::notifications-edit", ['only' => ['edit', 'update']]);
        $this->middleware("antares.can:antares/notifications::notifications-preview", ['only' => ['preview']]);
        $this->middleware("antares.can:antares/notifications::notifications-test", ['only' => ['sendtest']]);
        $this->middleware("antares.can:antares/notifications::notifications-change-status", ['only' => ['changeStatus']]);
        $this->middleware("antares.can:antares/notifications::notifications-create", ['only' => ['create', 'store']]);
        $this->middleware("antares.can:antares/notifications::notifications-list", ['only' => ['index']]);
        //$this->middleware("antares.can:antares/notifications::notifications-delete", ['only' => ['delete']]);        
    }

    /**
     * index default action
     * 
     * @param String $type
     * @return View
     */
    public function index($type = null)
    {
        return $this->processor->index($type);
    }

    /**
     * notification edit form
     * 
     * @param mixed $id
     * @param String $locale
     * @return RedirectResponse
     */
    public function edit($id, $locale = 'en')
    {
        return $this->processor->edit($id, $locale, $this);
    }

    /**
     * update single job
     * 
     * @param mixed $id
     * @return RedirectResponse
     */
    public function update()
    {
        return $this->processor->update($this);
    }

    /**
     * Response when storing command failed on validation.
     * @param  MessageBag|array  $errors
     * @return mixed
     */
    public function updateValidationFailed($id, $errors)
    {
        return $this->redirectWithErrors(handles('antares::notifications/edit/' . $id), $errors);
    }

    /**
     * when update job failed
     * 
     * @return RedirectResponse
     */
    public function updateFailed()
    {
        app('antares.messages')->add('error', trans('antares/notifications::messages.notification_update_failed'));
        return redirect()->back();
    }

    /**
     * when update job completed successfully
     * 
     * @return RedirectResponse
     */
    public function updateSuccess()
    {
        app('antares.messages')->add('success', trans('antares/notifications::messages.notification_update_success'));
        return redirect()->back();
    }

    /**
     * sends test notification
     * 
     * @param mixed $id
     * @return View
     */
    public function sendtest($id = null)
    {
        return $this->processor->sendtest($this, $id);
    }

    /**
     * when sending preview notification failed
     * 
     * @return RedirectResponse
     */
    public function sendFailed()
    {
        app('antares.messages')->add('error', trans('antares/notifications::messages.notification_preview_error'));
        return redirect()->back();
    }

    /**
     * when sending preview notification completed successfully
     * 
     * @return RedirectResponse
     */
    public function sendSuccess()
    {
        app('antares.messages')->add('success', trans('antares/notifications::messages.notification_preview_sent'));
        return redirect()->back();
    }

    /**
     * preview notification
     * 
     * @return View
     */
    public function preview()
    {
        return $this->processor->preview();
    }

    /**
     * changes notification status
     * 
     * @param String $id
     * @return RedirectResponse
     */
    public function changeStatus($id)
    {
        return $this->processor->changeStatus($this, $id);
    }

    /**
     * when changing notification status completed successfully
     * 
     * @return RedirectResponse
     */
    public function changeStatusSuccess()
    {
        app('antares.messages')->add('success', trans('antares/notifications::messages.notification_change_status_success'));
        return redirect()->back();
    }

    /**
     * when changing notification status failed
     * 
     * @return RedirectResponse
     */
    public function changeStatusFailed()
    {
        app('antares.messages')->add('error', trans('antares/notifications::messages.notification_change_status_failed'));
        return redirect()->back();
    }

    /**
     * create new notification notification
     * 
     * @param String $type
     * @return View
     */
    public function create($type = null)
    {
        return $this->processor->create($type);
    }

    /**
     * when storing new notification notification
     * 
     * @return RedirectResponse
     */
    public function store()
    {
        return $this->processor->store($this);
    }

    /**
     * when storing new notification notification failed on validation
     * 
     * @return RedirectResponse
     */
    public function storeValidationFailed($errors)
    {
        return $this->redirectWithErrors(url()->previous(), $errors);
    }

    /**
     * when creation notification notification completed successfully
     * 
     * @param String $level
     * @return RedirectResponse
     */
    public function createSuccess()
    {
        app('antares.messages')->add('error', trans('antares/notifications::messages.notification_create_success'));
        return redirect()->to(handles('antares::notifications/index'));
    }

    /**
     * when creation notification notification failed
     * 
     * @return RedirectResponse
     */
    public function createFailed()
    {
        app('antares.messages')->add('error', trans('antares/notifications::messages.notification_create_failed'));
        return redirect()->back();
    }

    /**
     * deletes custom notification
     * 
     * @param mixed $id
     * @return RedirectResponse
     */
    public function delete($id)
    {
        return $this->processor->delete($id, $this);
    }

    /**
     * when deletion of custom notification completed successfully
     * 
     * @return RedirectResponse
     */
    public function deleteSuccess()
    {
        app('antares.messages')->add('success', trans('antares/notifications::messages.notification_delete_success'));
        return redirect()->to(handles('antares::notifications/index'));
    }

    /**
     * when deletion of custom notification failed
     * 
     * @return RedirectResponse
     */
    public function deleteFailed()
    {
        app('antares.messages')->add('error', trans('antares/notifications::messages.notification_delete_failed'));
        return redirect()->to(handles('antares::notifications/index'));
    }

}
