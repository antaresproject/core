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



namespace Antares\Logger\Processor;

use Antares\Logger\Http\Breadcrumb\Breadcrumb;
use Antares\Logger\Http\Datatables\Devices;
use Antares\Foundation\Processor\Processor;
use Antares\Logger\Model\LogsLoginDevices;
use Antares\Logger\Http\Controllers\Admin\DevicesController as Listener;
use Antares\Logger\Http\Form\DeviceForm;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Exception;

class DeviceProcessor extends Processor
{

    /**
     * devices datatables instance
     *
     * @var Devices 
     */
    protected $datatables;

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * Construct a new Brand presenter.
     * 
     * @param Brands $datatables
     * @param Breadcrumb $breadcrumb
     */
    public function __construct(Devices $datatables, Breadcrumb $breadcrumb)
    {
        $this->datatables = $datatables;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * shows devices list
     * 
     * @return \Illuminate\View\View
     */
    public function table()
    {
        $this->breadcrumb->onDevicesList();
        return $this->datatables->render('antares/logger::admin.devices.index');
    }

    /**
     * on delete device
     * 
     * @param Listener $listener
     * @param mixes $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Listener $listener, $id)
    {
        try {
            $model = LogsLoginDevices::query()->findOrFail($id);
            if ($model->delete()) {
                return $listener->deviceDeletedSuccessfully();
            }
        } catch (Exception $ex) {
            Log::emergency($ex);
            return $listener->deviceDeletionFailed();
        }
    }

    /**
     * on edit device name
     * 
     * @param mixed $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $this->breadcrumb->onEditDevice($id);
        $model = app(LogsLoginDevices::class)->findOrFail($id);
        $form  = new DeviceForm($model);
        return view('antares/logger::admin.devices.edit', compact('form'));
    }

    /**
     * on update device name
     * 
     * @param Listener $listener
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Listener $listener, $id)
    {
        $model = LogsLoginDevices::query()->findOrFail($id);
        $form  = new DeviceForm($model);
        if (!$form->isValid()) {
            return $listener->deviceUpdateFailedValidation();
        }
        $model->name = Input::get('name');
        if (!$model->save()) {
            return $listener->deviceUpdateFailed();
        }
        return $listener->deviceUpdateSuccess();
    }

}
