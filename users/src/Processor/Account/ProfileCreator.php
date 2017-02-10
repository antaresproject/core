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
use Antares\Model\User as Eloquent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Antares\Support\Facades\Foundation;
use Antares\Contracts\Foundation\Command\Account\ProfileCreator as Command;
use Antares\Contracts\Foundation\Listener\Account\ProfileCreator as Listener;

class ProfileCreator extends User implements Command
{

    /**
     * View registration page.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\ProfileCreator  $listener
     *
     * @return mixed
     */
    public function create()
    {
        $eloquent = Foundation::make('antares.user');
        $form     = $this->presenter->profile($eloquent, 'antares::register');
        $form->extend(function ($form) {
            $form->submit = 'antares/foundation::title.register';
        });
        return compact('eloquent', 'form');
    }

    /**
     * Create a new user.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\ProfileCreator  $listener
     * @param  array  $input
     *
     * @return mixed
     */
    public function store(Listener $listener, array $input)
    {
        $user = Foundation::make('antares.user');
        $form = $this->presenter->profile($user);
        if (!$form->isValid()) {
            return $listener->createProfileFailedValidation($form->getMessageBag());
        }
        DB::beginTransaction();

        try {
            $this->saving($user, $input);
            $this->notifyCreatedUser($user);
        } catch (Exception $ex) {
            DB::rollback();
            Log::emergency($ex);
            return $listener->createProfileFailed(['error' => $ex->getMessage()]);
        }
        DB::commit();
        return $listener->profileCreated();
    }

    /**
     * Send new registration e-mail to user.
     *
     * @param  \Antares\Model\User  $user
     * @return mixed
     */
    protected function notifyCreatedUser(Eloquent $user)
    {
        return app('events')->fire('user-register-notification', [
                    'variables'  => ['user' => $user,],
                    'recipients' => [$user]]
        );
    }

    /**
     * Saving new user.
     *
     * @param  \Antares\Model\User  $user
     * @param  array  $input
     * @param  string  $password
     *
     * @return void
     */
    protected function saving(Eloquent $user, array $input)
    {
        $user->setAttribute('email', $input['email']);
        $user->setAttribute('firstname', $input['firstname']);
        $user->setAttribute('lastname', $input['lastname']);
        $user->setAttribute('password', $input['password']);
        $user->save();
        $user->roles()->sync([
            Config::get('antares/foundation::roles.member', 2),
        ]);
    }

}
