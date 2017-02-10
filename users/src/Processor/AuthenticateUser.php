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


namespace Antares\Users\Processor;

use Antares\Contracts\Auth\Command\ThrottlesLogins as ThrottlesCommand;
use Antares\Contracts\Auth\Listener\AuthenticateUser as Listener;
use Antares\Contracts\Auth\Command\AuthenticateUser as Command;
use Antares\Users\Validation\AuthenticateUser as Validator;
use Antares\Authorization\Factory as AclFactory;
use Illuminate\Contracts\Auth\Guard;
use Antares\Model\User as Eloquent;
use Validator as LaravelValidator;
use Antares\Messages\MessageBag;
use Illuminate\Support\Arr;

class AuthenticateUser extends Authenticate implements Command
{

    /**
     * AclFactory instance
     *
     * @var AclFactory 
     */
    protected $acl;

    /**
     * Create a new processor instance.
     *
     * @param  \Antares\Contracts\Auth\Guard  $auth
     * @param  \Antares\Foundation\Validation\AuthenticateUser  $validator
     * @param AclFactory $acl
     */
    public function __construct(Guard $auth, Validator $validator, AclFactory $acl)
    {
        parent::__construct($auth);
        $this->validator = $validator;
        $this->acl       = $acl;
    }

    /**
     * Login a user.
     *
     * @param  \Antares\Contracts\Auth\Listener\AuthenticateUser  $listener
     * @param  array  $input
     * @param  \Antares\Contracts\Auth\Command\ThrottlesLogins|null  $throttles
     *
     * @return mixed
     */
    public function login(Listener $listener, array $input, ThrottlesCommand $throttles = null)
    {
//        LaravelValidator::extend('custom', function($attribute, $value, $parameters, $validator) {
//            return event('validation.before.login', [$attribute, $value, $parameters, $validator]);
//        });

        $validation = $this->validator->on('login')->with($input);
        // Validate user login, if any errors is found redirect it back to
        // login page with the errors.
        if ($validation->fails()) {
            return $listener->userLoginHasFailedValidation($validation->getMessageBag());
        }

        if ($this->hasTooManyAttempts($throttles)) {
            return $this->handleUserHasTooManyAttempts($listener, $input, $throttles);
        }
        if ($this->authenticate($input)) {
            return $this->handleUserWasAuthenticated($listener, $input, $throttles);
        }

        return $this->handleUserFailedAuthentication($listener, $input, $throttles);
    }

    /**
     * Authenticate the user.
     *
     * @param  array  $input
     *
     * @return bool
     */
    protected function authenticate(array $input)
    {
        $remember = (isset($input['remember']) && $input['remember'] === 'yes');

        $data = Arr::except($input, ['remember']);

        // We should now attempt to login the user using Auth class. If this
        // failed simply return false.
        return $this->auth->attempt($data, $remember);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Antares\Contracts\Auth\Listener\AuthenticateUser  $listener
     * @param  array  $input
     * @param  \Antares\Contracts\Auth\Command\ThrottlesLogins|null  $throttles
     *
     * @return mixed
     */
    protected function handleUserWasAuthenticated(Listener $listener, array $input, ThrottlesCommand $throttles = null)
    {
        if ($throttles) {
            $throttles->clearLoginAttempts($input);
        }
        $redirect = ($this->acl->make('antares')->can('show-dashboard')) ? handles('antares::/') : handles('client');

        auth()->guard('web')->getSession()->remove('auth');
        $user = $this->getUser();

        if (!$this->verifyUserActive($user)) {
            $this->auth->logout();
            return $listener->userIsNotActive();
        }

        return $listener->userHasLoggedIn($user, $redirect);
    }

    /**
     * Send the response after the user has too many attempts.
     *
     * @param  \Antares\Contracts\Auth\Listener\AuthenticateUser  $listener
     * @param  array  $input
     * @param  \Antares\Contracts\Auth\Command\ThrottlesLogins|null  $throttles
     *
     * @return mixed
     */
    protected function handleUserHasTooManyAttempts(Listener $listener, array $input, ThrottlesCommand $throttles = null)
    {
        $throttles->incrementLoginAttempts($input);
        return $listener->sendLockoutResponse($input, $throttles->getSecondsBeforeNextAttempts($input));
    }

    /**
     * Send the response after the user failed authentication.
     *
     * @param  \Antares\Contracts\Auth\Listener\AuthenticateUser  $listener
     * @param  array  $input
     * @param  \Antares\Contracts\Auth\Command\ThrottlesLogins|null  $throttles
     *
     * @return mixed
     */
    protected function handleUserFailedAuthentication(Listener $listener, array $input, ThrottlesCommand $throttles = null)
    {
        if ($throttles) {
            $throttles->incrementLoginAttempts($input);
        }
        return $listener->userLoginHasFailedValidation(new MessageBag(['password' => 'Unable to login. User credentials are not valid.']));
    }

    /**
     * Check if user has too many attempts.
     *
     * @param  \Antares\Contracts\Auth\Command\ThrottlesLogins|null  $throttles
     *
     * @return bool
     */
    protected function hasTooManyAttempts(ThrottlesCommand $throttles = null)
    {
        return ($throttles && $throttles->hasTooManyLoginAttempts());
    }

    /**
     * Verify user account if has not been verified, other this should
     * be ignored in most cases.
     *
     * @param  \Antares\Model\User  $user
     *
     * @return \Antares\Model\User
     */
    protected function verifyWhenFirstTimeLogin(Eloquent $user)
    {
        if ((int) $user->getAttribute('status') === Eloquent::UNVERIFIED) {
            $user->activate()->save();
        }

        return $user;
    }

    /**
     * Verify user account if has not been verified, other this should
     * be ignored in most cases.
     *
     * @param  \Antares\Model\User  $user
     *
     * @return \Antares\Model\User
     */
    protected function verifyUserActive(Eloquent $user)
    {
        return (int) $user->getAttribute('status') === Eloquent::VERIFIED;
    }

}
