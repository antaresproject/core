<?php

/**
 * Part of the Antares package.
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
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Antares\Foundation\Processor\Setting as Processor;
use Antares\Contracts\Foundation\Listener\SystemUpdater;
use Antares\Contracts\Foundation\Listener\SettingUpdater;

class SettingsController extends AdminController implements SystemUpdater, SettingUpdater
{

    /**
     * Settings configuration Controller for the application.
     *
     * @param  \Antares\Foundation\Processor\Setting  $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;

        parent::__construct();
    }

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.manage', ['only' => 'edit', 'update', 'migrate']);
        $this->middleware('antares.csrf', ['only' => 'migrate']);
        $this->middleware('antares.forms:manage-antares', ['only' => 'edit', 'update', 'migrate']);
        $this->middleware('antares.can::change-app-settings', ['only' => ['edit', 'update'],]);
    }

    /**
     * Show Settings Page.
     *
     * GET (:antares)/settings
     *
     * @return mixed
     */
    public function edit()
    {
        return $this->processor->edit($this);
    }

    /**
     * Update Settings.
     *
     * POST (:antares)/settings
     *
     * @return mixed
     */
    public function update()
    {
        return $this->processor->update($this, Input::all());
    }

    /**
     * Update antares/foundation.
     *
     * @return mixed
     */
    public function migrate()
    {
        return $this->processor->migrate($this);
    }

    /**
     * Response when show setting page.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showSettingChanger(array $data)
    {
        set_meta('title', trans('antares/foundation::title.settings.list'));
        return view('antares/foundation::settings.index', $data);
    }

    /**
     * Response when update setting failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function settingFailedValidation($errors)
    {
        return $this->redirectWithErrors(handles('antares::settings/index'), $errors);
    }

    /**
     * Response when update setting succeed.
     *
     * @return mixed
     */
    public function settingHasUpdated()
    {
        $message = trans('antares/foundation::response.settings.update');

        return $this->redirectWithMessage(handles('antares::settings/index'), $message);
    }

    /**
     * Response when update Antares components succeed.
     *
     * @return mixed
     */
    public function systemHasUpdated()
    {
        $message = trans('antares/foundation::response.settings.system-update');
        return $this->redirectWithMessage(handles('antares::settings/index'), $message);
    }

}
