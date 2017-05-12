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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Users\Http\Controllers\Account;

use Illuminate\Support\Facades\Input;
use Antares\Users\Processor\Account\ProfileUpdater as Processor;
use Antares\Contracts\Foundation\Listener\Account\ProfileUpdater as Listener;

class ProfileUpdaterController extends Controller implements Listener
{

    /**
     * Construct Account Controller to allow user to update own profile.
     * Only authenticated user should be able to access this controller.
     *
     * @param  Processor  $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;

        parent::__construct();
    }

    /**
     * middleware configuration
     */
    public function setupMiddleware()
    {
        ;
    }

    /**
     * Edit user account/profile page.
     *
     * GET (:antares)/account
     *
     * @return mixed
     */
    public function edit()
    {
        return $this->processor->edit($this);
    }

    /**
     * POST Edit user account/profile.
     *
     * POST (:antares)/account
     *
     * @return mixed
     */
    public function update()
    {
        return $this->processor->update($this, Input::all());
    }

    /**
     * Response to show user profile changer.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showProfileChanger(array $data)
    {
        set_meta('title', trans('antares/foundation::title.account.profile'));
        return view('antares/foundation::account.index', $data);
    }

    /**
     * Response when validation on update profile failed.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function updateProfileFailedValidation($errors)
    {
        return $this->redirectWithErrors(handles('antares::account'), $errors);
    }

    /**
     * Response when update profile failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function updateProfileFailed(array $errors)
    {
        $message = trans('antares/foundation::response.db-failed', $errors);

        return $this->redirectWithMessage(handles('antares::account'), $message, 'error');
    }

    /**
     * Response when update profile succeed.
     *
     * @return mixed
     */
    public function profileUpdated()
    {
        $message = trans('antares/foundation::response.account.profile.update');

        return $this->redirectWithMessage(handles('antares::account'), $message);
    }

}
