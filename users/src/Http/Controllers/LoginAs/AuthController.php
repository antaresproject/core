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

namespace Antares\Users\Http\Controllers\LoginAs;

use Antares\Users\Processor\LoginAs\AuthProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;

class AuthController extends AdminController
{

    /**
     * processor instance
     *
     * @var Processor
     */
    protected $processor;

    /**
     * constructing
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * Define the middleware.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.can:antares/control::login-as-user', ['only' => ['login']]);
    }

    /**
     * login as secondary user and logout from primary
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login($id)
    {
        return $this->processor->login($this, $id);
    }

    /**
     * when user logging in completed successfully
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userLogInSuccessfully()
    {
        return $this->redirectWithMessage(handles('antares::'), trans('antares/foundation::messages.logged_as_user', ['name' => auth()->user()->fullname]), 'success');
    }

    /**
     * when user logging in failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userLogInFailed()
    {
        return $this->redirectWithMessage(handles('antares::'), trans('antares/foundation::messages.unable_to_login'), 'error');
    }

    /**
     * logout from secondary user and back to primary user
     * 
     * @param String $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout($key)
    {
        return $this->processor->logout($this, $key);
    }

    /**
     * when logging out from user area completed successfully
     * 
     * @param String $to
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userLogOutSuccessfully($to)
    {
        return $this->redirectWithMessage($to, trans('antares/foundation::messages.logged_out_from_user'), 'success');
    }

    /**
     * when logging out from user area failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userLogOutFailed()
    {
        return $this->redirectWithMessage(handles('antares::'), 'Invalid application key.', 'error');
    }

}
