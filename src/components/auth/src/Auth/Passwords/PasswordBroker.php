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


namespace Antares\Auth\Passwords;

use Antares\Contracts\Notification\Notification;
use Illuminate\Auth\Passwords\PasswordBroker as Broker;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as RemindableContract;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Event;
use Closure;
use function config;

class PasswordBroker extends Broker
{

    /**
     * The mailer instance.
     *
     * @var Notification
     */
    protected $mailer;

    /**
     * Create a new password broker instance.
     *
     * @param  TokenRepositoryInterface  $tokens
     * @param  UserProvider  $users
     * @param  \Antares\Contracts\Notification\Notification  $mailer
     * @param  string  $emailView
     */
    public function __construct(TokenRepositoryInterface $tokens, UserProvider $users, Notification $mailer, $emailView
    )
    {
        $this->users     = $users;
        $this->mailer    = $mailer;
        $this->tokens    = $tokens;
        $this->emailView = $emailView;
    }

    /**
     * Send a password reminder to a user.
     *
     * @param  array  $credentials
     * @param  \Closure  $callback
     *
     * @return string
     */
    public function sendResetLink(array $credentials, Closure $callback = null)
    {
// First we will check to see if we found a user at the given credentials and
// if we did not we will redirect back to this current URI with a piece of
// "flash" data in the session to indicate to the developers the errors.
        $user = $this->getUser($credentials);
        if (is_null($user)) {
            return PasswordBrokerContract::INVALID_USER;
        }
// Once we have the reminder token, we are ready to send a message out to the
// user with a link to reset their password. We will then redirect back to
// the current URI having nothing set in the session to indicate errors.
        $token = $this->tokens->create($user);
        $this->emailResetLink($user, $token, $callback);
        return PasswordBrokerContract::RESET_LINK_SENT;
    }

    /**
     * Send the password reminder e-mail.
     *
     * @param  CanResetPassword  $user
     * @param  string  $token
     * @param  \Closure|null  $callback
     *
     * @return \Antares\Contracts\Notification\Receipt
     */
    public function emailResetLink(RemindableContract $user, $token, Closure $callback = null)
    {

        $data = [
            'user'   => ($user instanceof Arrayable ? $user->toArray() : $user),
            'email'  => $user->getEmailForPasswordReset(),
            'token'  => $token,
            'url'    => handles('antares/foundation::forgot/reset/' . $token),
            'expire' => config('auth.passwords.users.expire', 60)
        ];
        email_notification('email.forgot_password', [$user], $data);
        return;
    }

}
