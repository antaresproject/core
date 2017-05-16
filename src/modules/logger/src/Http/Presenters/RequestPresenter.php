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

use Antares\Logger\Contracts\RequestPresenter as PresenterContract;
use Antares\Logger\Http\Breadcrumb\Breadcrumb;
use Yajra\Datatables\Facades\Datatables;
use Antares\Logger\Http\Datatables\RequestLogs;
use Antares\Logger\Http\Datatables\RequestLogDetails;

class RequestPresenter extends Presenter implements PresenterContract
{

    /**
     * Breadcrumb instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * datatables instance
     *
     * @var Datatables
     */
    protected $datatables;

    /**
     * request log details instance
     *
     * @var RequestLogDetails
     */
    protected $requestLogsDetails;

    /**
     * constructing
     * 
     * @param Breadcrumb $breadcrumb
     * @param RequestLogs $datatables
     * @param RequestLogDetails $requestLogsDetails
     */
    public function __construct(Breadcrumb $breadcrumb, RequestLogs $datatables, RequestLogDetails $requestLogsDetails)
    {
        $this->breadcrumb         = $breadcrumb;
        $this->datatables         = $datatables;
        $this->requestLogsDetails = $requestLogsDetails;
    }

    /**
     * Table View Generator for \Antares\Logger\Model\Logs.
     * 
     * @return \Illuminate\View\View
     */
    public function table()
    {
        $this->breadcrumb->onRequestIndex();
        return $this->datatables->render('antares/logger::admin.request.index');
    }

    /**
     * Table View Generator for \Antares\Logger\Entities\RequestLogEntryCollection.
     * 
     * @return \Illuminate\View\View
     */
    public function tableRequestLog()
    {
        $this->breadcrumb->onRequestDetails();
        return $this->requestLogsDetails->render('antares/logger::admin.request.index');
    }

}
