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

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Control\Contracts\Roles as RolesContract;
use Antares\Control\Processor\Role as RoleProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Input;
use Antares\Model\Role;

class RolesController extends AdminController implements RolesContract
{

    /**
     * Setup a new controller.
     *
     * @param  \Antares\Control\Processor\Role   $processor
     */
    public function __construct(RoleProcessor $processor)
    {
        $this->processor = $processor;
        parent::__construct();
    }

    /**
     * Define the filters.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.manage:roles');
        $this->middleware('antares.csrf', ['only' => 'delete']);
        $this->middleware('antares.can:antares/control::roles-list', ['only' => ['index'],]);
        $this->middleware('antares.can:antares/control::edit-role', ['only' => ['show', 'edit'],]);
        $this->middleware('antares.can:antares/control::create-role', ['only' => ['create', 'store'],]);
        $this->middleware('antares.can:antares/control::delete-role', ['only' => ['delete', 'destroy'],]);
    }

    /**
     * List all the roles.
     *
     * @return mixed
     */
    public function index()
    {
        set_meta('title', trans('antares/control::title.roles.list'));
        return $this->processor->index();
    }

    /**
     * Show a role.
     *
     * @param  int  $id
     *
     * @return mixed
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * Create a new role.
     *
     * @return mixed
     */
    public function create()
    {
        return $this->processor->create($this);
    }

    /**
     * Edit the role.
     *
     * @param  int  $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        return $this->processor->edit($this, $id);
    }

    /**
     * Edit the group ACL
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acl($id)
    {
        return $this->processor->acl($id);
    }

    /**
     * Acl tree as JsonResponse
     *
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree($id)
    {
        return $this->processor->tree($id);
    }

    /**
     * Create the role.
     *
     * @return mixed
     */
    public function store()
    {
        return $this->processor->store($this, Input::all());
    }

    /**
     * Update the role.
     *
     * @param  int  $id
     *
     * @return mixed
     */
    public function update($id)
    {

        return $this->processor->update($this, Input::all(), $id);
    }

    /**
     * Request to delete a role.
     *
     * @param  int  $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->destroy($id);
    }

    /**
     * Request to delete a role.
     *
     * @param  int  $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        return $this->processor->destroy($this, $id);
    }

    /**
     * Response when create role page succeed.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function createSucceed(array $data)
    {
        set_meta('title', trans('antares/control::title.roles.create'));
        return view('antares/control::roles.edit', $data);
    }

    /**
     * Response when edit role page succeed.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function editSucceed(array $data)
    {
        set_meta('title', trans('antares/control::title.roles.update'));
        return view('antares/control::roles.edit', $data);
    }

    /**
     * Response when storing role failed on validation.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function storeValidationFailed($errors)
    {
        return redirect()->back()->withErrors($errors)->withInput();
    }

    /**
     * Response when storing role failed.
     *
     * @param  array  $error
     *
     * @return mixed
     */
    public function storeFailed(array $error)
    {
        $message = trans('antares/foundation::response.db-failed', $error);
        return $this->redirectWithMessage(handles('antares::control/index/roles'), $message, 'error');
    }

    /**
     * Response when storing user succeed.
     *
     * @param  \Antares\Model\Role  $role
     *
     * @return mixed
     */
    public function storeSucceed(Role $role)
    {
        $message = trans('antares/control::response.roles.created', ['name' => $role->name]);
        return $this->redirectWithMessage(handles('antares::control/index/roles'), $message);
    }

    /**
     * Response when updating role failed on validation.
     *
     * @param  object  $messages
     * @param  int     $id
     *
     * @return mixed
     */
    public function updateValidationFailed($messages, $id)
    {
        return $this->redirectWithErrors(handles("antares::control/roles/{$id}/edit"), $messages);
    }

    /**
     * Response when updating role failed.
     *
     * @param  array  $error
     *
     * @return mixed
     */
    public function updateFailed(array $error)
    {
        $message = trans('antares/foundation::response.db-failed', $error);
        return $this->redirectWithMessage(handles('antares::control/index/roles'), $message, 'error');
    }

    /**
     * Response when updating role succeed.
     *
     * @return RedirectResponse
     */
    public function updateSucceed()
    {
        $message = trans('antares/control::response.roles.updated');
        return $this->redirectWithMessage(handles('antares::control/index/roles'), $message);
    }

    /**
     * Response when deleting role failed.
     *
     * @param  array  $error
     *
     * @return mixed
     */
    public function destroyFailed(array $error)
    {
        $message = trans('antares/foundation::response.db-failed', $error);
        return $this->redirectWithMessage(handles('antares::control/index/roles'), $message, 'error');
    }

    /**
     * Response when updating role succeed.
     *
     * @param  \Antares\Model\Role  $role
     *
     * @return mixed
     */
    public function destroySucceed(Role $role)
    {
        $message = trans('antares/control::response.roles.deleted', ['name' => $role->getAttribute('name')]);
        return $this->redirectWithMessage(handles('antares::control/index/roles'), $message);
    }

    /**
     * Response when user verification failed.
     *
     * @return mixed
     */
    public function userVerificationFailed()
    {
        return $this->suspend(500);
    }

}
