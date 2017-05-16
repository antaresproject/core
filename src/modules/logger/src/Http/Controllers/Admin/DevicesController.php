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

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Logger\Processor\DeviceProcessor;

class DevicesController extends AdminController
{

    /**
     * processor instance
     * `
     * @var DeviceProcessor 
     */
    protected $processor;

    /**
     * device processor
     * 
     * @param DeviceProcessor $processor
     */
    public function __construct(DeviceProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * middleware configuration
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
    }

    /**
     * shows user devices
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->table();
    }

    /**
     * on device edit name
     */
    public function edit($id)
    {
        return $this->processor->edit($id);
    }

    /**
     * on device update
     */
    public function update($id)
    {
        return $this->processor->update($this, $id);
    }

    /**
     * on device deletion
     */
    public function delete($id)
    {
        return $this->processor->delete($this, $id);
    }

    /**
     * device has been deleted successfully
     */
    public function deviceDeletedSuccessfully()
    {
        $message = trans('antares/logger::response.device.delete.success');
        return $this->redirectWithMessage(handles('antares::logger/devices/index'), $message);
    }

    /**
     * device has not been delete
     */
    public function deviceDeletionFailed()
    {
        $message = trans('antares/logger::response.device.delete.failed');
        return $this->redirectWithMessage(handles('antares::logger/devices/index'), $message, 'error');
    }

    /**
     * when update device form validation failed
     */
    public function deviceUpdateFailedValidation($errors)
    {
        app('antares.messages')->add('error', $errors);
        return $this->redirectWithErrors(handles('antares::logger/devices/index'), $errors);
    }

    /**
     * when device updated successfully
     */
    public function deviceUpdateFailed()
    {
        $message = trans('antares/logger::response.device.update.error');
        return $this->redirectWithMessage(handles('antares::logger/devices/index'), $message, 'error');
    }

    /**
     * when device update failed
     */
    public function deviceUpdateSuccess()
    {
        $message = trans('antares/logger::response.device.update.success');
        return $this->redirectWithMessage(handles('antares::logger/devices/index'), $message);
    }

}
