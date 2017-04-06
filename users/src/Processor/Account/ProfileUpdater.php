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

use Antares\Contracts\Foundation\Command\Account\ProfileUpdater as ProfileUpdaterContract;
use Antares\Contracts\Foundation\Listener\Account\ProfileUpdater as Listener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use SplFileInfo;
use Exception;

class ProfileUpdater extends User implements ProfileUpdaterContract
{

    /**
     * Get account/profile information.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\ProfileUpdater  $listener
     *
     * @return mixed
     */
    public function edit(Listener $listener)
    {

        $eloquent = user();
        $form     = $this->presenter->profile($eloquent, 'antares::account');
        Event::fire("antares.form: user.profile", [$eloquent, $form, "user.profile"]);

        return $listener->showProfileChanger(['eloquent' => $eloquent, 'form' => $form]);
    }

    /**
     * Update profile information.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\ProfileUpdater  $listener
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(Listener $listener, array $input)
    {
        $user = user();
        if (!$this->validateCurrentUser($user, $input)) {
            return $listener->abortWhenUserMismatched();
        }

        $validation = $this->validator->onUpdate()->with($input, 'user.profile.customfields.validate', ['name' => 'user.profile']);
        if ($validation->fails()) {
            return $listener->updateProfileFailedValidation($validation->getMessageBag());
        }
        try {
            $this->saving($user, $input);
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->updateProfileFailed(['error' => $e->getMessage()]);
        }

        return $listener->profileUpdated();
    }

    /**
     * Save user profile.
     *
     * @param  \Antares\Model\User|\Illuminate\Database\Eloquent\Model  $user
     * @param  array  $input
     *
     * @return void
     */
    protected function saving($user, array $input)
    {
        if (!env('APP_DEMO')) {
            $user->setAttribute('email', $input['email']);
        }
        $user->setAttribute('firstname', $input['firstname']);
        $user->setAttribute('lastname', $input['lastname']);
        if (isset($input['password']) && strlen($input['password'])) {
            $user->setAttribute('password', $input['password']);
        }

        $target = $this->movePicture($input);


        $this->fireEvent('updating', [$user]);
        $this->fireEvent('saving', [$user]);
        DB::transaction(function () use ($user, $target) {
            $user->save();
            if ($target) {
                $meta        = $user->meta()->getRelated()->query()->firstOrNew(['user_id' => $user->id, 'name' => 'picture']);
                $meta->value = $target;
                $meta->save();
            }
            $this->fireCustomFieldsEvent('profile.save', [$user, 'namespace' => 'user.profile']);
        });
        $this->fireEvent('updated', [$user]);
        $this->fireEvent('saved', [$user]);
    }

    /**
     * Move profile picture to destination directory
     * 
     * @param array $input
     * @return boolean
     * @throws Exception
     */
    protected function movePicture(array $input = [])
    {
        if (!is_null($picture = array_get($input, 'file'))) {
            if (!file_exists($picture)) {
                return false;
            }
            $file   = new SplFileInfo($picture);
            $target = public_path('avatars' . DIRECTORY_SEPARATOR . $file->getFilename());
            if (!File::move($picture, $target)) {
                throw new Exception('Unable to move profile picture file.');
            }
            return str_replace(public_path(), '', $target);
        }
        return false;
    }

}
