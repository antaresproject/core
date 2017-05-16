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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Translations\Http\Controllers\Admin;

use Antares\Translations\Processor\SyncProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Translations\Contracts\SyncListener;

class SyncController extends AdminController implements SyncListener
{

    /**
     * implments instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.can:antares/translations::translations-list', ['only' => ['index'],]);
    }

    /**
     * index default action
     * 
     * @return \Illuminate\View\View
     */
    public function index($area = null, $locale = null)
    {
        return $this->processor->index($this, !is_null($area) ? $area : area(), $locale);
    }

    /**
     * When sync failed
     * 
     * @param String $area
     * @param String $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncFailed($area, $locale)
    {
        app('antares.messages')->add('error', trans('antares/translations::messages.sync_failed'));
        return redirect(handles("antares::translations/index/$area/$locale"));
    }

    /**
     * When sync completed successfully
     * 
     * @param String $area
     * @param String $locale     
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncSuccess($area, $locale)
    {
        app('antares.messages')->add('success', trans('antares/translations::messages.sync_success'));
        return redirect(handles("antares::translations/index/$area/$locale"));
    }

}
