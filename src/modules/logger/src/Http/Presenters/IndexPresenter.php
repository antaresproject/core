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

use Antares\Logger\Contracts\IndexPresenter as PresenterContract;
use Antares\Logger\Http\Datatables\ErrorLogDetails;
use Antares\Logger\Http\Breadcrumb\Breadcrumb;
use Illuminate\Contracts\Container\Container;
use Antares\Logger\Http\Datatables\ErrorLogs;

class IndexPresenter extends Presenter implements PresenterContract
{

    /**
     * Breadcrumb instance
     *
     * @var Breadcrumb 
     */
    protected $breadcrumb;

    /**
     * error logs datatables instance
     *
     * @var ErrorLogs 
     */
    protected $errorLogsDatatable;

    /**
     * error logs details datatable instance
     *
     * @var ErrorLogDetails 
     */
    protected $errorLogDetailsTable;

    /**
     * constructing
     * 
     * @param Container $container
     */
    public function __construct(Breadcrumb $breadcrumb, ErrorLogs $errorLogsDatatable, ErrorLogDetails $errorLogDetailsTable)
    {
        $this->breadcrumb           = $breadcrumb;
        $this->errorLogsDatatable   = $errorLogsDatatable;
        $this->errorLogDetailsTable = $errorLogDetailsTable;
    }

    /**
     * presenter for details action
     * 
     * @param array $levels
     * @param \Arcanedev\Support\Collection $entries
     * @param String $level
     * @return \Illuminate\View\View
     */
    public function details($log, array $levels, $entries, $level = null)
    {
        $this->breadcrumb->onSystem();
        $dataTable = $this->errorLogDetailsTable->html();
        return $this->view('details', compact('dataTable', 'log', 'levels', 'entries', 'level'));
    }

    /**
     * Table View Generator for Antares\Updater\Version.
     * 
     * @return \Illuminate\View\View
     */
    public function table()
    {
        $this->breadcrumb->onSystem();
        return $this->errorLogsDatatable->render('antares/logger::admin.index.system');
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     *
     * @return \Illuminate\View\View
     */
    public function view($view, $data = [], $mergeData = [])
    {
        return view('antares/logger::admin.index.' . $view, $data, $mergeData);
    }

}
