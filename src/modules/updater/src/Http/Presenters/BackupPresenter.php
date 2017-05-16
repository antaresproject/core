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

use Antares\Updater\Contracts\BackupPresenter as PresenterContract;
use Antares\Updater\Http\Breadcrumb\Breadcrumb;
use Antares\Updater\Http\Datatables\Backups;

class BackupPresenter implements PresenterContract
{

    /**
     * breadcrumbs instance
     * 
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * backups datatable
     *
     * @var Backups 
     */
    protected $backups;

    /**
     * constructing
     * 
     * @param Breadcrumb $breadcrumb
     * @param Backups $backups
     */
    public function __construct(Breadcrumb $breadcrumb, Backups $backups)
    {
        $this->breadcrumb = $breadcrumb;
        $this->backups    = $backups;
    }

    /**
     * Table View Generator for Antares\Updater\Version.
     * 
     * @return \Illuminate\View\View
     */
    public function table()
    {
        publish('updater', ['js/update.js', 'js/restore.js']);
        return $this->backups->render('antares/updater::admin.backup.index');
    }

}
