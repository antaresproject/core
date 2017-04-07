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
use Antares\Installation\Progress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Antares\Html\Builder;

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
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        return $this->processor->store($this, $request->all());
    }

    /**
     * Show components selection form
     * GET (:antares)/install/components
     *
     * @return mixed
     */
    public function components(Progress $progress)
    {
        $progress->reset();
        $progress->save();

        return $this->processor->components($this);
    }

    /**
     * Show components selection form
     * POST (:antares)/install/components/store
     *
     * @param Request $request
     * @return mixed
     */
    public function storeComponents(Request $request)
    {
        $selected = (array) $request->get('optional', []);

        return $this->processor->storeComponents($this, $selected);
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
        return $this->redirect(handles('antares::install/license'));
    }

    /**
     * setting application license
     * 
     * @return View
     */
    public function license(Request $request)
    {
        return $this->processor->license($this, $request);
    }

    /**
     * Redirects when license validation failed.
     *
     * @param MessageBag $messageBag
     * @return RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function licenseFailedValidation(MessageBag $messageBag)
    {
        return $this->redirectWithErrors(handles('antares::install/license'), $messageBag);
    }

    /**
     * when license details has not been stored
     * 
     * @return RedirectResponse
     */
    public function licenseFailedStore()
    {
        return redirect_with_message(handles('antares::install/license'), trans('Unable to store license file.'), 'error');
    }

    /**
     * when license details has been stored
     * 
     * @return RedirectResponse
     */
    public function licenseSuccessStore()
    {
        return redirect_with_message(handles('antares::install/create'), trans('License details has been stored.'), 'success');
    }

    /**
     * shows license form
     * 
     * @param Builder $form
     * @return View
     */
    public function showLicenseForm($form)
    {
        return view('antares/installer::license', compact('form'));
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
     * When installation is failed.
     *
     * @param Progress $progress
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function failed(Progress $progress)
    {
        $additionalMessage = $progress->getFailedMessage();

        return view('antares/installer::installation.failed', compact('additionalMessage'));
    }

    /**
     * Returns the view about installation progress.
     *
     * @return RedirectResponse
     */
    public function showInstallProgress() {
        return redirect()->to(handles('antares::install/progress'));
    }

}
