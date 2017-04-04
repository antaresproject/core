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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Installation\Http\Controllers;

use Antares\Installation\Processor\Installer as InstallerProcessor;
use Antares\Foundation\Http\Controllers\BaseController;
use Antares\Installation\Processor\Installer;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;

class InstallerController extends BaseController
{

    /**
     * The layout that should be used for responses.
     */
    protected $layout = 'antares/foundation::layouts.installer.main';

    /**
     * Construct Installer controller.
     *
     * @param  Installer  $processor
     */
    public function __construct(InstallerProcessor $processor)
    {
        $this->processor = $processor;
        set_meta('navigation::usernav', false);
        set_meta('title', 'Installer');
        parent::__construct();
    }

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.installed', ['only' => ['index', 'create', 'store'],]);
    }

    /**
     * Check installation requirement page.
     *
     * GET (:antares)/install
     *
     * @return mixed
     */
    public function index()
    {

        app('antares.memory')->forgetCache();
        return $this->processor->index($this);
    }

    /**
     * Migrate database schema for Antares.
     *
     * GET (:antares)/install/prepare
     *
     * @return mixed
     */
    public function prepare()
    {
        return $this->processor->prepare($this);
    }

    /**
     * Show create adminstrator page.
     *
     * GET (:antares)/install/create
     *
     * @return mixed
     */
    public function create()
    {
        return $this->processor->create($this);
    }

    /**
     * Create an adminstrator.
     *
     * POST (:antares)/install/create
     *
     * @return mixed
     */
    public function store()
    {
        return $this->processor->store($this, Input::all());
    }

    /**
     * Show components selection form
     * GET (:antares)/install/components
     *
     * @return mixed
     */
    public function components()
    {
        return $this->processor->components($this);
    }

    /**
     * Show components selection form
     * POST (:antares)/install/components/store
     *
     * @return mixed
     */
    public function storeComponents()
    {
        return $this->processor->storeComponents($this, Input::all());
    }

    /**
     * End of installation.
     *
     * GET (:antares)/install/done
     *
     * @return mixed
     */
    public function done()
    {
        return $this->processor->done($this);
    }

    /**
     * Response for installation welcome page.
     *
     * @param  array   $data
     *
     * @return mixed
     */
    public function indexSucceed(array $data)
    {
        return view('antares/installer::index', $data);
    }

    /**
     * Response when installation is prepared.
     *
     * @return mixed
     */
    public function prepareSucceed()
    {
        return $this->redirect(handles('antares::install/create'));
    }

    /**
     * Response view to input user information for installation.
     *
     * @param  array   $data
     *
     * @return mixed
     */
    public function createSucceed(array $data)
    {
        return view('antares/installer::create', $data);
    }

    /**
     * Response when store installation config is failed.
     *
     * @return mixed
     */
    public function storeFailed()
    {
        return $this->redirect(handles('antares::install/create'));
    }

    /**
     * Response when store installation config is succeed.
     *
     * @return mixed
     */
    public function storeSucceed()
    {
        return $this->redirect(handles('antares::install/components'));
    }

    /**
     * Response for components selection page
     *
     * @param  array   $data
     * @return mixed
     */
    public function componentsSucceed(array $data)
    {
        return view('antares/installer::components', $data);
    }

    /**
     * Response when installation is done.
     *
     * @return mixed
     */
    public function doneSucceed()
    {
        app('antares.messages')->add('success', trans('Installation is completed. Now you can login to administration area.'));
        return $this->redirect(handles('antares::install/completed'));
    }

    /**
     * Response when installation throws exception and is not done.
     *
     * @return mixed
     */
    public function doneFailed()
    {
        app('antares.messages')->add('error', trans('Installation failed. Please try again or contact with software provider.'));
        return $this->redirect(handles('antares::install/failed'));
    }

    /**
     * when installation is completed
     * 
     * @return View
     */
    public function completed()
    {
        return view('antares/installer::installation.completed');
    }

    /**
     * when installation is failed
     * 
     * @return View
     */
    public function failed()
    {
        return view('antares/installer::installation.failed');
    }

}
