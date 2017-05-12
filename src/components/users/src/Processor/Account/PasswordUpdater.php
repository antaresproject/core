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

namespace Antares\Users\Processor\Account;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Antares\Model\User as Eloquent;
use Illuminate\Support\Facades\Log;
use Antares\Contracts\Foundation\Command\Account\PasswordUpdater as Command;
use Antares\Contracts\Foundation\Listener\Account\PasswordUpdater as Listener;

class PasswordUpdater extends User implements Command
{

    /**
     * Get password information.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\PasswordUpdater  $listener
     *
     * @return mixed
     */
    public function edit(Listener $listener)
    {
        $eloquent = Auth::user();
        $form     = $this->presenter->password($eloquent);

        return $listener->showPasswordChanger(['eloquent' => $eloquent, 'form' => $form]);
    }

    /**
     * Update password information.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\PasswordUpdater  $listener
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(Listener $listener, array $input)
    {
        $user = Auth::user();

        if (!$this->validateCurrentUser($user, $input)) {
            return $listener->abortWhenUserMismatched('User mismatched.');
        }

        $validation = $this->validator->on('changePassword')->with($input);

        if ($validation->fails()) {
            return $listener->updatePasswordFailedValidation($validation->getMessageBag());
        }

        if (!Hash::check($input['current_password'], $user->password)) {
            return $listener->verifyCurrentPasswordFailed();
        }

        try {
            $this->saving($user, $input);
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->updatePasswordFailed(['error' => $e->getMessage()]);
        }

        return $listener->passwordUpdated();
    }

    /**
     * Saving new password.
     *
     * @param  \Antares\Model\User $user
     * @param  array  $input
     */
    protected function saving(Eloquent $user, array $input)
    {
        if (!env('APP_DEMO')) {
            $user->setAttribute('password', $input['new_password']);
            DB::transaction(function () use ($user) {
                $user->save();
            });
        }
    }

}
