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

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Users\Processor\Account\PasswordBroker as Processor;
use Illuminate\Support\Facades\Input;
use Antares\Contracts\Auth\Listener\PasswordReset;
use Antares\Contracts\Auth\Listener\PasswordResetLink;

class PasswordBrokerController extends AdminController implements PasswordResetLink, PasswordReset
{

    /**
     * Construct Forgot Password Controller with some pre-define
     * configuration.
     *
     * @param \Antares\Users\Processor\Account\PasswordBroker  $processor
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
        $this->middleware('antares.guest');
    }

    /**
     * Show Forgot Password Page where user can enter their current e-mail
     * address.
     *
     * GET (:antares)/forgot
     *
     * @return mixed
     */
    public function create()
    {
        set_meta('title', trans('antares/foundation::title.forgot-password'));
        return view('antares/foundation::forgot.index');
    }

    /**
     * Validate requested e-mail address for password reset, we should first
     * send a URL where user need to visit before the system can actually
     * change the password on their behave.
     *
     * POST (:antares)/forgot
     *
     * @return mixed
     */
    public function store()
    {
        return $this->processor->store($this, Input::all());
    }

    /**
     * Once user actually visit the reset my password page, we now should be
     * able to make the operation to create a new password.
     *
     * GET (:antares)/forgot/reset/(:hash)
     *
     * @param  string  $token
     *
     * @return mixed
     */
    public function show($token)
    {
        set_meta('title', trans('antares/foundation::title.reset-password'));
        return view('antares/foundation::forgot.reset')->with('token', $token);
    }

    /**
     * Create a new password for the user.
     *
     * POST (:antares)/forgot/reset
     *
     * @return mixed
     */
    public function update()
    {
        $input = Input::only('email', 'password', 'password_confirmation', 'token');

        return $this->processor->update($this, $input);
    }

    /**
     * Response when request password failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function resetLinkFailedValidation($errors)
    {
        return $this->redirectWithErrors(handles('antares::forgot'), $errors);
    }

    /**
     * Response when request reset password failed.
     *
     * @param  string  $response
     *
     * @return mixed
     */
    public function resetLinkFailed($response)
    {
        $message = trans($response);

        return $this->redirectWithMessage(handles('antares::forgot'), $message, 'error');
    }

    /**
     * Response when request reset password succeed.
     *
     * @param  string  $response
     *
     * @return mixed
     */
    public function resetLinkSent($response)
    {
        $message = trans($response);
        return $this->redirectWithMessage(handles('antares::forgot'), $message);
    }

    /**
     * Response when reset password failed.
     *
     * @param  string  $response
     *
     * @return mixed
     */
    public function passwordResetHasFailed($response)
    {
        $message = trans($response);
        $token   = Input::get('token');

        return $this->redirectWithMessage(handles("antares::forgot/reset/{$token}"), $message, 'error');
    }

    /**
     * Response when reset password succeed.
     *
     * @param  string  $response
     *
     * @return mixed
     */
    public function passwordHasReset($response)
    {
        $message = trans('antares/foundation::response.account.password.update');

        return $this->redirectWithMessage(handles('antares::/'), $message);
    }

}
