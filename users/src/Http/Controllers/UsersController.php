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


namespace Antares\Users\Http\Controllers;

use Antares\Contracts\Foundation\Listener\Account\UserCreator;
use Antares\Contracts\Foundation\Listener\Account\UserRemover;
use Antares\Contracts\Foundation\Listener\Account\UserUpdater;
use Antares\Contracts\Foundation\Listener\Account\UserViewer;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Users\Processor\User as Processor;
use Antares\Users\Processor\ProfilePicture;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use Antares\Model\User;

class UsersController extends AdminController implements UserCreator, UserRemover, UserUpdater, UserViewer
{

    /**
     * CRUD Controller for Users management using resource routing.
     *
     * @param  \Antares\Users\Processor\User  $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
        parent::__construct();
    }

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.csrf', ['only' => 'delete']);
        $this->middleware('antares.forms:manage-users');
        $this->middleware('antares.can:clients-list', ['only' => ['index'],]);
        $this->middleware('antares.can:client-create', ['only' => ['create', 'store'],]);
        $this->middleware('antares.can:client-update', ['only' => ['edit', 'update'],]);
        $this->middleware('antares.can:client-delete', ['only' => ['delete', 'destroy'],]);
    }

    /**
     * List all the users.
     *
     * GET (:antares)/users
     *
     * @return mixed
     */
    public function index()
    {
        set_meta('title', trans('antares/foundation::title.clients.list_breadcrumb'));
        return $this->processor->index($this, Input::all());
    }

    /**
     * shows user details
     */
    public function show($id)
    {
        set_meta('title', trans('antares/foundation::title.clients.show'));
        return $this->processor->show($id);
    }

    /**
     * Create a new user.
     *
     * GET (:antares)/users/create
     *
     * @return mixed
     */
    public function create()
    {
        return $this->processor->create($this);
    }

    /**
     * Edit the user.
     *
     * GET (:antares)/users/$id/edit
     * @param  int|string  $id
     * @return mixed
     */
    public function edit($id)
    {
        return $this->processor->edit($this, $id);
    }

    /**
     * Create the user.
     *
     * POST (:antares)/users
     *
     * @return mixed
     */
    public function store()
    {
        return $this->processor->store($this, Input::all());
    }

    /**
     * Update the user.
     *
     * PUT (:antares)/users/1
     *
     * @param  int|string  $id
     *
     * @return mixed
     */
    public function update($id)
    {
        return $this->processor->update($this, $id, Input::all());
    }

    /**
     * Request to delete a user.
     *
     * GET (:antares)/$id/delete
     *
     * @param  int|string  $id
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        return $this->destroy($id);
    }

    /**
     * Request to delete a user.
     *
     * DELETE (:antares)/$id
     *
     * @param  int|string  $id
     *
     * @return mixed
     */
    public function destroy($id = null)
    {

        return $this->processor->destroy($this, $id);
    }

    /**
     * Response when list users page succeed.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showUsers(array $data)
    {
        set_meta('title', trans('antares/foundation::title.users.list'));
        return view('antares/foundation::users.index', $data);
    }

    /**
     * Response when create user page succeed.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showUserCreator(array $data)
    {
        set_meta('title', trans('antares/foundation::title.users.create'));
        return view('antares/foundation::users.edit', $data);
    }

    /**
     * Response when edit user page succeed.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showUserChanger(array $data)
    {
        set_meta('title', trans('antares/foundation::title.users.update'));

        return view('antares/foundation::users.edit', $data);
    }

    /**
     * Response when storing user failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function createUserFailedValidation($errors)
    {
        return $this->redirectWithErrors(handles('antares::users/create'), $errors);
    }

    /**
     * Response when storing user failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function createUserFailed(array $errors)
    {
        $message = trans('antares/foundation::response.db-failed', $errors);

        return $this->redirectWithMessage(handles('antares::users/index'), $message, 'error');
    }

    /**
     * Response when storing user succeed.
     *
     * @return mixed
     */
    public function userCreated()
    {
        $message = trans('antares/foundation::response.users.create');
        return $this->redirectWithMessage(handles('antares::users/index'), $message);
    }

    /**
     * Response when update user failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     * @param  string|int  $id
     *
     * @return mixed
     */
    public function updateUserFailedValidation($errors, $id)
    {
        return $this->redirectWithErrors(handles("antares::users/{$id}/edit"), $errors);
    }

    /**
     * Response when updating user failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function updateUserFailed(array $errors)
    {
        $message = trans('antares/foundation::response.db-failed', $errors);
        return $this->redirectWithMessage(url()->previous(), $message, 'error');
    }

    /**
     * Response when updating user succeed.
     *
     * @return mixed
     */
    public function userUpdated()
    {
        $message = trans('antares/foundation::response.users.update');
        return $this->redirectWithMessage(url()->previous(), $message);
    }

    /**
     * Response when destroying user failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function userDeletionFailed(array $errors)
    {
        $message = trans('antares/foundation::response.db-failed', $errors);

        return $this->redirectWithMessage(handles('antares::users/index'), $message, 'error');
    }

    /**
     * Response when destroying user succeed.
     *
     * @return mixed
     */
    public function userDeleted()
    {
        $message = trans('antares/foundation::response.users.delete');

        return $this->redirectWithMessage(handles('antares::users/index'), $message);
    }

    /**
     * Response when destroying user succeed.
     *
     * @return mixed
     */
    public function usersDeleted()
    {
        $message = trans_choice('antares/foundation::response.users.delete', 2);
        return $this->redirectWithMessage(URL::previous(), $message);
    }

    /**
     * Response when user tried to self delete.
     *
     * @return mixed
     */
    public function selfDeletionFailed()
    {
        return $this->suspend(404);
    }

    /**
     * Response when user verification failed.
     *
     * @return mixed
     */
    public function abortWhenUserMismatched()
    {
        return $this->suspend(500);
    }

    /**
     * Uploads profile picture
     * 
     * @param ProfilePicture $processor
     * @return \Illuminate\Http\JsonResponse
     */
    public function picture(ProfilePicture $processor)
    {
        return $processor->picture();
    }

    /**
     * Set profile picture as gravatar
     * 
     * @param ProfilePicture $processor
     * @return \Illuminate\Http\Response
     */
    public function gravatar(ProfilePicture $processor)
    {
        $processor->gravatar();
        return $this->redirectWithMessage(URL::previous(), trans('antares/foundation::response.users.gravatar_has_been_set'));
    }

    /**
     * Users list
     * 
     * @return JsonResponse
     */
    public function elements()
    {
        $input   = Input::all();
        $builder = User::query();
        if (!is_null($query   = array_get($input, 'q')) and ! is_null($field   = array_get($input, 'field'))) {
            $builder->where($field, 'like', '%' . e($query) . '%');
        }

        $paginate = $builder->paginate(array_get($input, 'per_page', 20));

        return new JsonResponse($paginate, 200);
    }

    /**
     * Changes user status as dependable action
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function status($id = null)
    {
        return $this->processor->status($id);
    }

}
