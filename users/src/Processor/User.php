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

use Antares\Contracts\Foundation\Listener\Account\UserCreator as UserCreatorListener;
use Antares\Contracts\Foundation\Listener\Account\UserRemover as UserRemoverListener;
use Antares\Contracts\Foundation\Listener\Account\UserUpdater as UserUpdaterListener;
use Antares\Contracts\Foundation\Command\Account\UserCreator as UserCreatorCommand;
use Antares\Contracts\Foundation\Command\Account\UserRemover as UserRemoverCommand;
use Antares\Contracts\Foundation\Command\Account\UserUpdater as UserUpdaterCommand;
use Antares\Contracts\Foundation\Listener\Account\UserViewer as UserViewerListener;
use Antares\Contracts\Foundation\Command\Account\UserViewer as UserViewerCommand;
use Antares\Users\Http\Presenters\User as Presenter;
use Antares\Routing\Traits\ControllerResponseTrait;
use Antares\Users\Validation\User as Validator;
use Antares\Foundation\Processor\Processor;
use Antares\Support\Facades\Foundation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Antares\Model\User as Eloquent;
use Illuminate\Support\Facades\DB;
use Antares\Model\Role;
use Exception;

class User extends Processor implements UserCreatorCommand, UserRemoverCommand, UserUpdaterCommand, UserViewerCommand
{

    use ControllerResponseTrait;

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
     * @param  \Antares\Contracts\Foundation\Listener\Account\UserViewer  $listener
     * @param  array  $input
     *
     * @return mixed
     */
    public function index(UserViewerListener $listener, array $input = [])
    {
        return $this->presenter->table();
    }

    /**
     * shows user details
     * 
     * @param mixed $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $model = Foundation::make('antares.user')->findOrFail($id);
        return $this->presenter->show($model);
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
        Event::fire("antares.form: user.profile", [$eloquent, $form, "user.profile"]);

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
        $eloquent = Foundation::make('antares.user')->findOrFail($id);
        $form     = $this->presenter->form($eloquent, 'update');
        $this->fireEvent('form', [$eloquent, $form]);
        Event::fire("antares.form: user.profile", [$eloquent, $form, "user.profile"]);
        return $listener->showUserChanger(compact('eloquent', 'form'));
    }

    /**
     * Store a user.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\UserCreator  $listener
     * @param  array   $input
     * @return mixed
     */
    public function store(UserCreatorListener $listener, array $input)
    {
        $user         = Foundation::make('antares.user');
        $user->status = Eloquent::UNVERIFIED;
        if (isset($input['password'])) {
            $user->password = $input['password'];
        }

        $form = $this->presenter->form($user, 'create');

        if (!$form->isValid()) {
            return $listener->createUserFailedValidation($form->getMessageBag());
        }
        DB::beginTransaction();
        try {
            $this->saving($user, $input, 'create');
        } catch (Exception $e) {
            Log::emergency($e);
            DB::rollback();
            return $listener->createUserFailed(['error' => $e->getMessage()]);
        }
        DB::commit();

        return $listener->userCreated();
    }

    /**
     * Update a user.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\UserUpdater  $listener
     * @param  string|int  $id
     * @param  array  $input
     * @return mixed
     */
    public function update(UserUpdaterListener $listener, $id, array $input)
    {

        if ((string) $id !== array_get($input, 'id') && !request()->wantsJson()) {
            return $listener->abortWhenUserMismatched();
        }
        $user = Foundation::make('antares.user')->withoutGlobalScopes()->findOrFail($id);
        $form = $this->presenter->form($user);

        if (!$form->isValid()) {
            return $listener->updateUserFailedValidation($form->getMessageBag(), $id);
        }
        !empty($input['password']) && $user->password = $input['password'];

        try {
            $this->saving($user, $input, 'update');
        } catch (Exception $e) {
            Log::emergency($e);
            event('notification.user_has_not_been_updated', ['variables' => ['user' => $user]]);
            return $listener->updateUserFailed(['error' => $e->getMessage()]);
        }
        event('notification.user_has_been_updated', ['variables' => ['user' => $user]]);
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

        if (!is_null($users = input('attr'))) {
            DB::transaction(function () use ($users) {
                foreach ($users as $uid) {
                    $user = Foundation::make('antares.user')->findOrFail($uid);
                    $this->fireEvent('deleting', [$user]);
                    $user->delete();
                    $this->fireEvent('deleted', [$user]);
                }
            });
            return $listener->usersDeleted();
        }

        $user = Foundation::make('antares.user')->findOrFail($id);
        if ((string) $user->id === (string) user()->id) {
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



        $this->fireEvent($beforeEvent, [$user]);
        $this->fireEvent('saving', [$user]);

        DB::beginTransaction();

        try {
            $roles = ($user->exists) ? $user->roles->pluck('id')->toArray() : Role::members()->get()->pluck('id')->toArray();
            $user->save();
            $user->roles()->sync($roles);
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
        DB::commit();



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
     * Change user status
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function status($id = null)
    {

        if (is_null($id) && !empty($ids = input('attr'))) {
            $table  = (new Eloquent())->getTable();
            $result = DB::table($table)->whereIn('id', $ids)->update(['status' => DB::raw('NOT status')]);
        } else {
            $model         = Eloquent::query()->findOrFail($id);
            $status        = !$model->status;
            $model->status = $status;
            $result        = $model->save();
        }
        $url = url()->previous();
        if (!$result) {
            return $this->redirectWithMessage($url, trans('antares/users::messages.dependable.status_has_not_been_changed'), 'error');
        }
        return $this->redirectWithMessage($url, trans('antares/users::messages.dependable.status_has_been_changed'));
    }

}
