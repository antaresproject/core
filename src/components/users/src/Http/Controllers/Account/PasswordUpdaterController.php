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
use Antares\Users\Processor\Account\PasswordUpdater as Processor;
use Antares\Contracts\Foundation\Listener\Account\PasswordUpdater as Listener;

class PasswordUpdaterController extends Controller implements Listener
{

    /**
     * Construct Account Controller to allow user to update own profile.
     * Only authenticated user should be able to access this controller.
     *
     * @param  \Antares\Users\Processor\Account\PasswordUpdater  $processor
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
     * Edit change password page.
     *
     * GET (:antares)/account/password
     *
     * @return mixed
     */
    public function edit()
    {
        return $this->processor->edit($this);
    }

    /**
     * POST Edit change password.
     *
     * POST (:antares)/account/password
     *
     * @return mixed
     */
    public function update()
    {
        return $this->processor->update($this, Input::all());
    }

    /**
     * Response to show user password.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showPasswordChanger(array $data)
    {
        set_meta('title', trans('antares/foundation::title.account.password'));

        return view('antares/foundation::account.password', $data);
    }

    /**
     * Response when validation on change password failed.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function updatePasswordFailedValidation($errors)
    {
        return $this->redirectWithErrors(handles('antares::account/password'), $errors);
    }

    /**
     * Response when verify current password failed.
     *
     * @return mixed
     */
    public function verifyCurrentPasswordFailed()
    {
        $message = trans('antares/foundation::response.account.password.invalid');

        return $this->redirectWithMessage(handles('antares::account/password'), $message, 'error');
    }

    /**
     * Response when update password failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function updatePasswordFailed(array $errors)
    {
        $message = trans('antares/foundation::response.db-failed', $errors);

        return $this->redirectWithMessage(handles('antares::account/password'), $message, 'error');
    }

    /**
     * Response when update password succeed.
     *
     * @return mixed
     */
    public function passwordUpdated()
    {
        $message = trans('antares/foundation::response.account.password.update');

        return $this->redirectWithMessage(handles('antares::account/password'), $message);
    }

}
