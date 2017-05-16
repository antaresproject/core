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

use Antares\Logger\Contracts\SystemPresenter as PresenterContract;
use Antares\Logger\Http\Breadcrumb\Breadcrumb;
use Antares\Logger\Adapter\ServerAdapter;

class SystemPresenter implements PresenterContract
{

    /**
     * server adapter instance
     *
     * @var $serverAdapter ServerAdapter
     */
    protected $serverAdapter;

    /**
     * Breadcrumb instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * constructing
     * 
     * @param ServerAdapter $serverAdapter
     */
    public function __construct(ServerAdapter $serverAdapter, Breadcrumb $breadcrumb)
    {
        $this->serverAdapter = $serverAdapter;
        $this->breadcrumb    = $breadcrumb;
    }

    /**
     * default presenter index action
     */
    public function index()
    {
        $this->breadcrumb->onSystemInformations();
        publish('logger', 'scripts.reports');
        $data = $this->serverAdapter->verify();
        return view('antares/logger::admin.system.index', $data);
    }

}
