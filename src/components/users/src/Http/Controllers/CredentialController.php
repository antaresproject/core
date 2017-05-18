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

namespace Antares\Users\Http\Controllers;

use Antares\Contracts\Auth\Listener\AuthenticateUser as AuthenticateListener;
use Antares\Contracts\Auth\Listener\ThrottlesLogins as ThrottlesListener;
use Antares\Contracts\Auth\Command\ThrottlesLogins as ThrottlesCommand;
use Antares\Foundation\Http\Controllers\AdminController;
use Illuminate\Contracts\Auth\Authenticatable;
use Antares\Users\Processor\AuthenticateUser;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Antares\Model\User;

class CredentialController extends AdminController implements AuthenticateListener, ThrottlesListener
{

    /**
     * Setup controller middleware.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.guest', ['only' => ['index', 'login']]);
    }

    /**
     * Login page.
     *
     * GET (:antares)/login
     *
     * @return mixed
     */
    public function index()
    {
        $select = User::select(['email'])->whereHas('roles', function($query) {
            $query->where('name', area());
        });
        $model      = $select->first();
        $attributes = !is_null($model) ? ['email' => $model->email, 'password' => 'demo'] : [];

        return view('antares/foundation::credential.login', $attributes);
    }

    /**
     * POST Login the user.
     *
     * POST (:antares)/login
     *
     * @return mixed
     */
    public function login(Request $request, AuthenticateUser $authenticate, ThrottlesCommand $throttles)
    {

        $input = $request->only(['email', 'password', 'remember']);

        $throttles->setRequest($request)->setLoginKey('email');
        return $authenticate->login($this, $input, $throttles);
    }

    /**
     * Logout the user.
     *
     * DELETE (:bundle)/login
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        auth()->logout();
        messages('success', trans('antares/foundation::response.credential.logged-out'));
        return Redirect::intended(handles(Input::get('redirect', handles('antares/foundation::login'))));
    }

    /**
     * Response to user log-in trigger failed validation .
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function userLoginHasFailedValidation($errors)
    {
        return $this->redirectWithErrors(handles(URL::previous()), $errors);
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  array  $input
     * @param  int  $seconds
     *
     * @return mixed
     */
    public function sendLockoutResponse(array $input, $seconds)
    {
        $message = trans('auth.throttle', ['seconds' => $seconds]);
        return $this->redirectWithMessage(handles('antares/foundation::login'), $message, 'error')->withInput();
    }

    /**
     * Response to user has logged in successfully.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param String $redirect
     * @return mixed
     */
    public function userHasLoggedIn(Authenticatable $user, $redirect)
    {
        messages('success', trans('antares/foundation::response.credential.logged-in'));
        return Redirect::intended($redirect);
    }

    /**
     * Response to user when account is not active
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userIsNotActive()
    {
        $message = trans('antares/foundation::response.credential.user-not-active');
        return $this->redirectWithMessage(handles(URL::previous()), $message, 'error');
    }

}
