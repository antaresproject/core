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



namespace Antares\Logger\Http\Presenters;

use Antares\Logger\Contracts\ActivityPresenter as PresenterContract;
use Antares\Logger\Http\Datatables\ActivityLogs as Datatable;
use Antares\Logger\Http\Breadcrumb\Breadcrumb;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

class ActivityPresenter extends Presenter implements PresenterContract
{

    /**
     * Breadcrumb instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * datatable instance
     *
     * @var Datatable 
     */
    protected $datatable;

    /**
     * constructing
     * 
     * @param Container $container
     */
    public function __construct(Breadcrumb $breadcrumb, Datatable $datatable)
    {
        $this->breadcrumb = $breadcrumb;
        $this->datatable  = $datatable;
    }

    /**
     * Table View Generator for \Antares\Logger\Model\Logs.
     * 
     * @param String $type
     * @return View
     */
    public function table($type = null)
    {
        publish('logger', 'scripts.reports');
        return $this->datatable->render('antares/logger::admin.activity.index');
    }

    /**
     * show details about activity log
     * 
     * @param Model $model
     * @return View
     */
    public function show($model)
    {
        $this->breadcrumb->onActivityDetails($model);
        $data = $model->toArray();
        return view('antares/logger::admin.activity.show', compact('data'));
    }

}
