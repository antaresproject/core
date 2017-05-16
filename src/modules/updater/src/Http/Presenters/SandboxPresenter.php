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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Http\Presenters;

use Antares\Updater\Contracts\SandboxPresenter as PresenterContract;
use Antares\Updater\Http\Datatables\Sandboxes;
use Illuminate\Contracts\Container\Container;

class SandboxPresenter implements PresenterContract
{

    /**
     * application container
     * 
     * @var Container
     */
    protected $container;

    /**
     * sandboxes datatable
     *
     * @var Sandboxes
     */
    protected $sandboxes;

    /**
     * constructing
     * 
     * @param Container $container
     * @param Sandboxes $sandboxes
     */
    public function __construct(Container $container, Sandboxes $sandboxes)
    {

        $this->container = $container;
        $this->sandboxes = $sandboxes;
    }

    /**
     * Table View Generator for Antares\Updater\Version.
     * 
     * @return \Illuminate\View\View
     */
    public function table()
    {
        publish('updater', ['js/widget.js']);
        $adapter = app('antares.version')->getAdapter();
        $adapter->retrive();
        $actual  = $adapter->getNextVersion();
        return $this->sandboxes->render('antares/updater::admin.sandbox.index', ['actual' => $actual]);
    }

}
