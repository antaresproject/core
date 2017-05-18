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


namespace Antares\Users\Processor\Account;

use Illuminate\Support\Facades\Auth;
use Antares\Model\User as Eloquent;
use Antares\Support\Facades\Foundation;
use Antares\Foundation\Processor\Processor;
use Antares\Contracts\Auth\Listener\PasswordReset;
use Antares\Contracts\Auth\Listener\PasswordResetLink;
use Illuminate\Contracts\Auth\PasswordBroker as Password;
use Antares\Contracts\Auth\Command\PasswordBroker as Command;
use Antares\Users\Validation\AuthenticateUser as Validator;

class PasswordBroker extends Processor implements Command
{

    /**
     * The password broker implementation.
     *
     * @var \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected $password;

    /**
     * Create a new processor instance.
     *
     * @param \Antares\Foundation\Validation\AuthenticateUser  $validator
     * @param \Illuminate\Contracts\Auth\PasswordBroker  $password
     */
    public function __construct(Validator $validator, Password $password)
    {
        $this->validator = $validator;
        $this->password  = $password;
    }

    /**
     * Request to reset password.
     *
     * @param  \Antares\Contracts\Auth\Listener\PasswordResetLink  $listener
     * @param  array  $input
     *
     * @return mixed
     */
    public function store(PasswordResetLink $listener, array $input)
    {
        $validation = $this->validator->with($input);
        if ($validation->fails()) {
            return $listener->resetLinkFailedValidation($validation->getMessageBag());
        }

        $memory = Foundation::memory();
        $site   = $memory->get('site.name', 'Antares');
        $data   = ['email' => $input['email']];

        $response = $this->password->sendResetLink($data, function ($mail) use ($site) {
            $mail->subject(trans('antares/foundation::email.forgot.request', ['site' => $site]));
        });

        if ($response != Password::RESET_LINK_SENT) {
            return $listener->resetLinkFailed($response);
        }

        return $listener->resetLinkSent($response);
    }

    /**
     * Reset the password.
     *
     * @param  \Antares\Contracts\Auth\Listener\PasswordReset  $listener
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(PasswordReset $listener, array $input)
    {
        $response = $this->password->reset($input, function (Eloquent $user, $password) {
            $user->setAttribute('password', $password);
            $user->save();

            Auth::login($user);
        });

        $errors = [
            Password::INVALID_PASSWORD,
            Password::INVALID_TOKEN,
            Password::INVALID_USER,
        ];

        if (in_array($response, $errors)) {
            return $listener->passwordResetHasFailed($response);
        }

        return $listener->passwordHasReset($response);
    }

}
