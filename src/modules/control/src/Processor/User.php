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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Control\Processor;

use Antares\Control\Contracts\Listener\Account\UserCreator as UserCreatorListener;
use Antares\Control\Contracts\Listener\Account\UserRemover as UserRemoverListener;
use Antares\Control\Contracts\Listener\Account\UserUpdater as UserUpdaterListener;
use Antares\Control\Http\Presenters\User as Presenter;
use Antares\Users\Validation\User as Validator;
use Antares\Support\Facades\Foundation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Antares\Model\User as Eloquent;
use Illuminate\Support\Facades\DB;
use Exception;

class User extends Processor
{

    /**
     * Create a new processor instance.
     *
     * @param  \Antares\Users\Http\Presenters\User  $presenter
     * @param  \Antares\Users\Validation\User  $validator
     */
    public function __construct(Presenter $presenter, Validator $validator)
    {
        $this->presenter = $presenter;
        $this->validator = $validator;
    }

    /**
     * View list users page.
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->presenter->table();
    }

    /**
     * View create user page.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\UserCreator  $listener
     *
     * @return mixed
     */
    public function create(UserCreatorListener $listener)
    {
        $eloquent = Foundation::make('antares.user');
        $form     = $this->presenter->form($eloquent, 'create');
        $this->fireEvent('form', [$eloquent, $form]);
        return $listener->showUserCreator(compact('eloquent', 'form'));
    }

    /**
     * View edit user page.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\UserUpdater  $listener
     * @param  string|int  $id
     *
     * @return mixed
     */
    public function edit(UserUpdaterListener $listener, $id)
    {
        $eloquent = Foundation::make('antares.user');
        if ($id !== auth()->user()->id) {
            $eloquent = $eloquent->where('id', $id)->withoutGlobalScopes()->whereHas('roles', function ($query) {
                        $role    = user()->roles->first();
                        $roles   = $role->getChilds();
                        $roles[] = $role->id;
                        $query->whereIn('role_id', array_values($roles));
                    })->first();
            if (is_null($eloquent)) {
                return $listener->noAccessToEdit();
            }
        } else {
            $eloquent = $eloquent->findOrFail($id);
        }

        $form = $this->presenter->form($eloquent, 'update');
        $this->fireEvent('form', [$eloquent, $form]);

        return $listener->showUserChanger(compact('eloquent', 'form'));
    }

    /**
     * Store a user.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\UserCreator  $listener
     * @param  array   $input
     *
     * @return mixed
     */
    public function store(UserCreatorListener $listener, array $input)
    {

        $user           = Foundation::make('antares.user');
        $user->status   = Eloquent::UNVERIFIED;
        $user->password = $input['password'];

        $form = $this->presenter->form($user);
        if (!$form->isValid()) {
            return $listener->createUserFailedValidation($form->getMessageBag());
        }

        try {
            $this->saving($user, $input, 'create');
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->createUserFailed(['error' => $e->getMessage()]);
        }

        return $listener->userCreated();
    }

    /**
     * Update a user.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\UserUpdater  $listener
     * @param  string|int  $id
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(UserUpdaterListener $listener, $id, array $input)
    {
        if ((string) $id !== $input['id']) {
            return $listener->abortWhenUserMismatched();
        }
        $user           = Foundation::make('antares.user')->withoutGlobalScopes()->findOrFail($id);
        !empty($input['password']) && $user->password = $input['password'];
        try {
            $this->saving($user, $input, 'update');
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->updateUserFailed(['error' => $e->getMessage()]);
        }

        return $listener->userUpdated();
    }

    /**
     * Destroy a user.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\UserRemover  $listener
     * @param  string|int  $id
     *
     * @return mixed
     */
    public function destroy(UserRemoverListener $listener, $id)
    {
        if (user()->id == $id) {
            return $listener->userDeletionFailed(['error' => trans('antares/control::messages.unable_to_delete_self')]);
        }
        $user = Foundation::make('antares.user');
        if (app('antares.acl')->make('antares/control')->can('login-as-user')) {
            $user = $user->withoutGlobalScopes();
        }

        $user = $user->findOrFail($id);
        if ((string) $user->id === (string) Auth::user()->id) {
            return $listener->selfDeletionFailed();
        }

        try {
            $this->fireEvent('deleting', [$user]);

            DB::transaction(function () use ($user) {
                $user->delete();
            });

            $this->fireEvent('deleted', [$user]);
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->userDeletionFailed(['error' => $e->getMessage()]);
        }

        return $listener->userDeleted();
    }

    /**
     * Save the user.
     *
     * @param  \Antares\Model\User  $user
     * @param  array  $input
     * @param  string  $type
     *
     * @return bool
     */
    protected function saving(Eloquent $user, $input = [], $type = 'create')
    {
        $beforeEvent = ($type === 'create' ? 'creating' : 'updating');
        $afterEvent  = ($type === 'create' ? 'created' : 'updated');

        $user->firstname = $input['firstname'];
        $user->lastname  = $input['lastname'];
        $user->email     = $input['email'];

        if (isset($input['status'])) {
            $user->status = $input['status'];
        }
        if ($user->exists && !isset($input['status'])) {
            $user->status = 0;
        }

        $this->fireEvent($beforeEvent, [$user]);
        $this->fireEvent('saving', [$user]);

        DB::transaction(function () use ($user, $input) {
            $user->save();
            $user->roles()->sync($input['roles']);
        });

        $this->fireEvent($afterEvent, [$user]);
        $this->fireEvent('saved', [$user]);

        return true;
    }

    /**
     * Fire Event related to eloquent process.
     *
     * @param  string  $type
     * @param  array   $parameters
     *
     * @return void
     */
    protected function fireEvent($type, array $parameters = [])
    {
        Event::fire("antares.{$type}: users", $parameters);
        Event::fire("antares.{$type}: user.account", $parameters);
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::users/{id}/edit');
    }

}
