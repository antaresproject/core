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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\Http\Controllers\Admin;

use Antares\Customfields\Processor\FieldProcessor as Processor;
use Antares\Customfields\Contracts\FieldCreator as Creator;
use Antares\Customfields\Contracts\FieldUpdater as Updater;
use Antares\Customfields\Contracts\FieldRemover as Remover;
use Antares\Customfields\Contracts\FieldViewer as Viewer;
use Antares\Foundation\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Input;
use Illuminate\Routing\Route;

class IndexController extends AdminController implements Creator, Updater, Remover, Viewer
{

    /**
     * constructing
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
        parent::__construct();
    }

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware("antares.can:antares/customfields::list-customfields", ['only' => ['show']]);
        $this->middleware('antares.can:antares/customfields::add-customfield', ['only' => ['create', 'store']]);
        $this->middleware('antares.can:antares/customfields::update-customfield', ['only' => ['edit', 'update']]);
        $this->middleware('antares.can:antares/customfields::delete-customfield', ['only' => ['delete', 'destroy']]);
    }

    /**
     * ------------ VIEW ----------------
     */

    /**
     * shows list of elements
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->show();
    }

    /**
     * ------------ CREATE ----------------
     */

    /**
     * create new custom field
     */
    public function create(Route $route)
    {
        return $this->processor->create($this, $route);
    }

    /**
     * store posted element
     */
    public function store()
    {
        return $this->processor->store($this, Input::all());
    }

    /**
     * shows custom field creator
     * @param array $data
     */
    public function showFieldCreator(array $data)
    {
        set_meta('title', trans('antares/customfields::title.create'));
        return view('antares/customfields::admin.edit', $data);
    }

    /**
     * response when custom field created
     */
    public function fieldCreated()
    {
        $message = trans('antares/customfields::response.create.success');
        return $this->redirectWithMessage(handles('antares::customfields/index'), $message);
    }

    /**
     * shows form when custom field validation failed
     * @param type $errors
     */
    public function createFieldFailedValidation($errors)
    {

        return $this->redirectWithErrors(handles('antares::customfields/create'), $errors);
    }

    /**
     * respone when create custom field failed (ex.: db error)
     * @param array $errors
     */
    public function createFieldFailed(array $errors)
    {
        $message = trans('antares/customfields::response.create.db-failed', $errors);
        return $this->redirectWithMessage(handles('antares::customfields/create'), $message, 'error');
    }

    /**
     * ------------ UPDATE ----------------
     */

    /**
     * Edit customfield
     * 
     * @param  int  $id
     * @return mixed
     */
    public function edit($id, Route $route)
    {
        return $this->processor->edit($this, $id, $route);
    }

    /**
     * updates customfield
     */
    public function update($id)
    {
        return $this->processor->update($this, $id, Input::all());
    }

    /**
     * shows custom field update form
     * @param array $data
     */
    public function showFieldUpdater(array $data)
    {
        set_meta('title', trans('antares/customfields::title.update'));
        return view('antares/customfields::admin.edit', $data);
    }

    /**
     * response when custom field update failed (ex.: db error)
     * @param array $errors
     */
    public function updateFieldFailed(array $errors)
    {
        $message = trans('antares/customfields::response.update.db-failed', $errors);
        return $this->redirectWithMessage(handles('antares::customfields/index'), $message, 'error');
    }

    /**
     * shows form when update validation failed
     * @param type $errors
     */
    public function updateFieldFailedValidation($errors, $id)
    {
        return $this->redirectWithErrors(handles("antares::customfields/{$id}/edit"), $errors);
    }

    /**
     * response when custom field updated
     */
    public function fieldUpdated()
    {
        $message = trans('antares/customfields::response.update.success');
        return $this->redirectWithMessage(handles('antares::customfields/index'), $message);
    }

    /**
     * ------------ REMOVE ----------------
     */

    /**
     * deletes custom field
     * @param type $id
     * @return type
     */
    public function delete($id)
    {
        return $this->destroy($id);
    }

    /**
     * Request to delete a custom field.
     * DELETE (:antares)/$id
     * @param  int|string  $id
     * @return mixed
     */
    public function destroy($id)
    {
        return $this->processor->destroy($this, $id);
    }

    /**
     * response when remove custom field failed
     * @param array $errors
     */
    public function removeFieldFailed(array $errors)
    {
        $message = trans('antares/customfields::response.delete.db-error');
        return $this->redirectWithMessage(handles('antares::customfields/index'), $message, 'error');
    }

    /**
     * response when custom field has been removed
     */
    public function fieldRemoved()
    {
        $message = trans('antares/customfields::response.delete.success');
        return $this->redirectWithMessage(handles('antares::customfields/index'), $message);
    }

    /**
     * response when model not found
     */
    public function abortWhenFieldMismatched()
    {
        return $this->suspend(500);
    }

}
