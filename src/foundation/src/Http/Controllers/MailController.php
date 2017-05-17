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

use Antares\Foundation\Processor\Mail as Processor;
use Illuminate\Support\Facades\Input;

class MailController extends AdminController
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
        $this->middleware('antares.manage', ['only' => 'index', 'update']);
    }

    /**
     * Show Settings Page.
     *
     * GET (:antares)/settings
     *
     * @return mixed
     */
    public function index()
    {
        set_meta('title', trans('antares/foundation::title.settings.list'));
        $data = $this->processor->index();
        return view('antares/foundation::settings.index', $data);
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
     * Response when update setting failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function settingFailedValidation($errors)
    {
        return $this->redirectWithErrors(handles('antares::settings/mail'), $errors);
    }

    /**
     * Response when update setting succeed.
     *
     * @return mixed
     */
    public function settingHasUpdated()
    {
        $message = trans('antares/foundation::response.settings.update');
        return $this->redirectWithMessage(handles('antares::settings/mail'), $message);
    }

}
