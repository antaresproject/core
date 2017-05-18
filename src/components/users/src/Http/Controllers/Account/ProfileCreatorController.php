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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Users\Http\Controllers\Account;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Users\Processor\Account\ProfileCreator as Processor;
use Antares\Contracts\Foundation\Listener\Account\ProfileCreator as Listener;

class ProfileCreatorController extends AdminController implements Listener
{

    /**
     * Registration Controller routing. It should only be accessible if
     * registration is allowed through the setting.
     *
     * @param  \Antares\Users\Processor\Account\ProfileCreator  $processor
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
        //$this->middleware('antares.registrable');
    }

    /**
     * User Registration Page.
     *
     * GET (:antares)/register
     *
     * @return mixed
     */
    public function create()
    {
        set_meta('title', trans('antares/foundation::title.register'));
        $data = $this->processor->create($this);
        if (isset($data['errors'])) {
            return $this->redirectWithErrors(handles('register'), $data['errors']);
        }
        return view('antares/foundation::credential.register', $data);
    }

    /**
     * Create a new user.
     *
     * POST (:antares)/register
     *
     * @return mixed
     */
    public function store()
    {
        return $this->processor->store($this, Input::all());
    }

    /**
     * Response when create a user failed validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function createProfileFailedValidation($errors)
    {
        return $this->redirectWithErrors(handles('register/create'), $errors);
    }

    /**
     * Response when create a user failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function createProfileFailed(array $errors)
    {
        messages('error', trans('antares/users::messages.register.db-failed', $errors));
        return $this->redirect(handles('register/create'))->withInput();
    }

    /**
     * Response when create a user succeed but unable to notify the user.
     *
     * @return mixed
     */
    public function profileCreatedWithoutNotification()
    {
        messages('success', trans('antares/foundation::response.users.create'));
        messages('error', trans('antares/foundation::response.credential.register.email-fail'));
        return Redirect::intended(handles('antares::login'));
    }

    /**
     * Response when create a user succeed with notification.
     *
     * @return mixed
     */
    public function profileCreated()
    {
        messages('success', trans('antares/foundation::response.users.create'));
        messages('success', trans('antares/foundation::response.credential.register.email-send'));

        return Redirect::intended(handles('antares::login'));
    }

}
