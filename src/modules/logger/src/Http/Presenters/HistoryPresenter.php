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

use Antares\Logger\Contracts\HistoryPresenter as PresenterContract;
use Antares\Logger\Http\Breadcrumb\Breadcrumb;
use Antares\Logger\Http\Datatables\Reports;

class HistoryPresenter extends Presenter implements PresenterContract
{

    /**
     * Breadcrumb instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * reports datatable
     *
     * @var \Antares\Logger\Http\Datatables\Reports
     */
    protected $reports;

    /**
     * constructing
     * 
     * @param Breadcrumb $breadcrumb
     * @param Reports $reports
     */
    public function __construct(Breadcrumb $breadcrumb, Reports $reports)
    {
        $this->breadcrumb = $breadcrumb;
        $this->reports    = $reports;
    }

    /**
     * show report details
     * 
     * @param mixed $eloquent
     * @return \Illuminate\View\View
     */
    public function show($eloquent)
    {
        return $this->view('show', $eloquent);
    }

    /**
     * Table View Generator
     * 
     * @return \Illuminate\View\View
     */
    public function table()
    {
        $this->breadcrumb->onReportsHistory();
        return $this->reports->render('antares/logger::admin.history.index');
    }

}
