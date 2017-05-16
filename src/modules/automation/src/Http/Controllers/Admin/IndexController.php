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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation\Http\Controllers\Admin;

use Antares\Automation\Processor\IndexProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Automation\Http\Datatables\AutomationLogs;
use Antares\Automation\Http\Breadcrumb\Breadcrumb;
use Antares\Automation\Contracts\IndexListener;
use Antares\Automation\Model\JobResults;

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
        $this->middleware("antares.can:antares/automation::automation-list", ['only' => ['index']]);
        $this->middleware("antares.can:antares/automation::automation-run", ['only' => ['run']]);
        $this->middleware("antares.can:antares/automation::automation-details", ['only' => ['show']]);
        $this->middleware("antares.can:antares/automation::automation-edit", ['only' => ['edit', 'update']]);
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
     * shows job details
     * 
     * @param mixed $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return $this->processor->show($id, $this);
    }

    /**
     * when show job details failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showFailed()
    {
        $message = trans('Automation job has not been found.');
        app('antares.messages')->add('error', $message);
        return redirect()->back();
    }

    /**
     * job edit form
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        return $this->processor->edit($id, $this);
    }

    /**
     * update single job
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        return $this->processor->update($this);
    }

    /**
     * Response when storing command failed on validation.
     * @param  \Illuminate\Support\MessageBag|array  $errors
     * @return mixed
     */
    public function updateValidationFailed($id, $errors)
    {
        return $this->redirectWithErrors(handles('antares::automation/edit/' . $id), $errors);
    }

    /**
     * when update job failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFailed()
    {
        $message = trans('Automation job has not been updated.');
        app('antares.messages')->add('error', $message);
        return redirect()->back();
    }

    /**
     * when update job completed successfully
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSuccess()
    {
        $message = trans('Automation job has been updated.');
        app('antares.messages')->add('success', $message);
        return redirect()->to(handles('antares::automation/index'));
    }

    /**
     * runs single job
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function run($id)
    {
        return $this->processor->run($id, $this);
    }

    /**
     * response when run job failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function runFailed()
    {
        $message = trans('Automation job cannot be launched. Error appears while trying to run job handler.');
        app('antares.messages')->add('error', $message);
        return redirect()->to(handles('antares::automation/index'));
    }

    /**
     * response when run job success
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function runSuccess()
    {
        $message = trans('Automation job has been added to queue.');
        app('antares.messages')->add('success', $message);
        return redirect()->to(handles('antares::automation/index'));
    }

    /**
     * shows automation logs
     * 
     * @return \Illuminate\View\View
     */
    public function logs(AutomationLogs $datatable)
    {
        $count = app(JobResults::class)->count();
        if (!$count) {
            app(Breadcrumb::class)->onInit();
        }

        return $datatable->render('antares/automation::admin.index.logs');
    }

    /**
     * Gets scripts for filter
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function scripts()
    {
        return $this->processor->scripts();
    }

    /**
     * Download automation logs
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download()
    {
        return $this->processor->download();
    }

    /**
     * Deletes automation logs
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete()
    {
        return $this->processor->delete($this);
    }

    /**
     * When delete of automation logs failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFailed()
    {
        app('antares.messages')->add('error', trans('antares/automation::messages.automation_delete_error'));
        return redirect()->to(handles('antares::automations/logs/index'));
    }

    /**
     * When date range of automation logs return empty collection
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function noLogsToDelete()
    {
        app('antares.messages')->add('error', trans('antares/automation::messages.automation_delete_no_logs'));
        return redirect()->to(handles('antares::automations/logs/index'));
    }

    /**
     * When automation log has been deleted
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSuccess()
    {
        app('antares.messages')->add('success', trans('antares/automation::messages.automation_delete_success'));
        return redirect()->to(handles('antares::automations/logs/index'));
    }

}
