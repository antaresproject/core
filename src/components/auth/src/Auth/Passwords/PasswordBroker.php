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

use Illuminate\Auth\Passwords\PasswordBroker as Broker;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;
use Illuminate\Contracts\Auth\UserProvider;
use Antares\Notifications\Facade\Notification;
use Closure;

class PasswordBroker extends Broker
{

    /**
     * The mailer instance.
     *
     * @var Notification
     */
    protected $mailer;

    /**
     * PasswordBroker constructor.
     * @param TokenRepositoryInterface $tokens
     * @param UserProvider $users
     * @param $emailView
     */
    public function __construct(TokenRepositoryInterface $tokens, UserProvider $users, $emailView)
    {
        parent::__construct($tokens, $users);

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
        $user->sendPasswordResetNotification($token);

        return PasswordBrokerContract::RESET_LINK_SENT;
    }

}
