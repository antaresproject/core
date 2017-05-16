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

namespace Antares\Control\Http\Controllers;

use Antares\Control\Contracts\Listener\Account\UserViewer;
use Antares\Control\Contracts\Listener\Account\UserCreator;
use Antares\Control\Contracts\Listener\Account\UserRemover;
use Antares\Control\Contracts\Listener\Account\UserUpdater;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Control\Processor\User as Processor;
use Illuminate\Support\Facades\Input;

class UsersController extends AdminController implements UserCreator, UserRemover, UserUpdater, UserViewer
{

    /**
     * CRUD Controller for Users management using resource routing.
     *
     * @param \Antares\Foundation\Processor\User $processor        	
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
        $this->middleware('antares.manage:users');
        $this->middleware('antares.csrf', ['only' => 'delete']);

        $this->middleware('antares.forms:user-update');
        $this->middleware('antares.can:antares/control::admin-list', ['only' => ['index'],]);
        $this->middleware('antares.can:antares/control::user-create', ['only' => ['create', 'store'],]);
        $this->middleware('antares.can:antares/control::user-update', ['only' => ['update', 'edit', 'store'],]);
        $this->middleware('antares.can:antares/control::user-delete', ['only' => ['delete', 'destroy'],]);
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
        set_meta('title', trans('List of admin users'));
        return $this->processor->index();
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
     *
     * @param int|string $id        	
     *
     * @return mixed
     */
    public function edit($id)
    {
        return $this->processor->edit($this, $id);
    }

    /**
     * redirects when there are not permissions to edit
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function noAccessToEdit()
    {
        $message = trans('antares/control::response.no-access-to-edit');
        return $this->redirectWithMessage(handles('antares::control/index/users'), $message, 'error');
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
     * @param int|string $id        	
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
     * @param int|string $id        	
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->destroy($id);
    }

    /**
     * Request to delete a user.
     *
     * DELETE (:antares)/$id
     *
     * @param int|string $id        	
     *
     * @return mixed
     */
    public function destroy($id)
    {
        return $this->processor->destroy($this, $id);
    }

    /**
     * Response when list users page succeed.
     *
     * @param array $data        	
     *
     * @return mixed
     */
    public function showUsers(array $data)
    {
        set_meta('title', trans('antares/control::title.users.list'));
        return view('antares/control::users.index', $data);
    }

    /**
     * Response when create user page succeed.
     *
     * @param array $data        	
     *
     * @return mixed
     */
    public function showUserCreator(array $data)
    {
        set_meta('title', trans('antares/control::title.users.create'));
        return view('antares/control::users.edit', $data);
    }

    /**
     * Response when edit user page succeed.
     *
     * @param array $data        	
     *
     * @return mixed
     */
    public function showUserChanger(array $data)
    {
        set_meta('title', trans('antares/control::title.users.update'));
        return view('antares/control::users.edit', $data);
    }

    /**
     * Response when storing user failed on validation.
     *
     * @param \Illuminate\Support\MessageBag|array $errors        	
     *
     * @return mixed
     */
    public function createUserFailedValidation($errors)
    {
        return $this->redirectWithErrors(handles('antares::control/users/create'), $errors);
    }

    /**
     * Response when storing user failed.
     *
     * @param array $errors        	
     *
     * @return mixed
     */
    public function createUserFailed(array $errors)
    {
        $message = trans('antares/control::response.db-failed', $errors);
        return $this->redirectWithMessage(handles('antares::control/index/users'), $message, 'error');
    }

    /**
     * Response when storing user succeed.
     *
     * @return mixed
     */
    public function userCreated()
    {
        $message = trans('antares/control::response.users.created');

        return $this->redirectWithMessage(handles('antares::control/index/users'), $message);
    }

    /**
     * Response when update user failed on validation.
     *
     * @param \Illuminate\Support\MessageBag|array $errors        	
     * @param string|int $id        	
     *
     * @return mixed
     */
    public function updateUserFailedValidation($errors, $id)
    {
        return $this->redirectWithErrors(handles("antares::control/users/{$id}/edit"), $errors);
    }

    /**
     * Response when updating user failed.
     *
     * @param array $errors        	
     *
     * @return mixed
     */
    public function updateUserFailed(array $errors)
    {
        $message = trans('antares/control::response.db-failed', $errors);
        return $this->redirectWithMessage(handles('antares::control/index/users'), $message, 'error');
    }

    /**
     * Response when updating user succeed.
     *
     * @return mixed
     */
    public function userUpdated()
    {
        $message = trans('antares/control::response.users.updated');
        return $this->redirectWithMessage(handles('antares::control/index/users'), $message);
    }

    /**
     * Response when destroying user failed.
     *
     * @param array $errors        	
     *
     * @return mixed
     */
    public function userDeletionFailed(array $errors)
    {
        $message = trans('antares/control::response.db-failed', $errors);
        return $this->redirectWithMessage(handles('antares::control/index/users'), $message, 'error');
    }

    /**
     * Response when destroying user succeed.
     *
     * @return mixed
     */
    public function userDeleted()
    {

        $message = trans('antares/control::response.users.deleted');

        return $this->redirectWithMessage(handles('antares::control/index/users'), $message);
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

}
