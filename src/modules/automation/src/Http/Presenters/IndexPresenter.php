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

namespace Antares\Automation\Http\Presenters;

use Antares\Automation\Contracts\IndexPresenter as PresenterContract;
use Antares\Automation\Http\Breadcrumb\Breadcrumb;
use Antares\Html\Form\FormBuilder;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Antares\Automation\Http\Datatables\Automation as AutomationDatatables;
use Antares\Automation\Http\Datatables\AutomationDetails;

class IndexPresenter implements PresenterContract
{

    /**
     * application container
     * 
     * @var Container
     */
    protected $container;

    /**
     * config container
     *
     * @var array
     */
    protected $config;

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * datatables instance
     *
     * @var Breadcrumb
     */
    protected $automationDatatables;

    /**
     * automation details datatable
     *
     * @var AutomationDetails
     */
    protected $automationDetailsDatatable;

    /**
     * constructing
     * 
     * @param Container $container
     * @param Breadcrumb $breadcrumb
     * @param AutomationDatatables $automationDatatables
     * @param AutomationDetails $automationDetailsDatatable
     */
    public function __construct(Container $container, Breadcrumb $breadcrumb, AutomationDatatables $automationDatatables, AutomationDetails $automationDetailsDatatable)
    {
        $this->container                  = $container;
        $this->config                     = config('antares/automation');
        $this->breadcrumb                 = $breadcrumb;
        $this->automationDatatables       = $automationDatatables;
        $this->automationDetailsDatatable = $automationDetailsDatatable;
    }

    /**
     * show report details
     * 
     * @param Model $eloquent
     * @return View
     */
    public function show($eloquent)
    {
        return $this->view('show', ['model' => $eloquent]);
    }

    /**
     * Table View Generator
     * 
     * @param Model $model
     * @return View
     */
    public function tableShow(Model $model)
    {
        $this->breadcrumb->onShow($model);
        return $this->automationDetailsDatatable->render('antares/automation::admin.index.show');
    }

    public function table()
    {
        $this->breadcrumb->onList();
        if (app('request')->ajax()) {
            return $this->automationDatatables->ajax();
        }
        $dataTable = $this->automationDatatables->html();
        return view('antares/automation::admin.index.index', compact('dataTable'));
    }

    /**
     * shows form edit job
     * 
     * @param Model $eloquent
     * @return View
     */
    public function edit($eloquent)
    {

        $this->breadcrumb->onEdit($eloquent);
        $configuration = $eloquent->value;
        if (!class_exists($configuration['classname'])) {
            throw new ModelNotFoundException();
        }
        $command = $this->container->make($configuration['classname']);
        $form    = $command->form($eloquent->toArray() + $configuration);
        return $this->view('edit', compact('form'));
    }

    /**
     * gets instance of command form
     * 
     * @param Model $eloquent
     * @return FormBuilder
     */
    public function form($eloquent)
    {
        $configuration = unserialize($eloquent->value);
        $command       = $this->container->make($configuration['classname']);
        return $command->form($eloquent->toArray() + $configuration);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     *
     * @return View
     */
    public function view($view, $data = [], $mergeData = [])
    {
        return view('antares/automation::admin.index.' . $view, $data, $mergeData);
    }

}
