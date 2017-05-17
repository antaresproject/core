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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Http\Controllers\Admin;

use Antares\UI\UIComponents\Processor\UpdateProcessor as Processor;
use Antares\UI\UIComponents\Contracts\Updater as UpdateContract;
use Antares\Foundation\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Input;

class UpdateController extends AdminController implements UpdateContract
{

    /**
     * Implements instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.can:antares/ui-components::ui-component-update', ['only' => ['update', 'edit'],]);
    }

    /**
     * Shows the form for editing the specified resource.
     * GET /ui-components/{id}/edit
     *
     * @param  mixed  $id
     * @return Response
     */
    public function edit($id)
    {
        return $this->processor->edit($this, $id);
    }

    /**
     * Update the specified resource in storage.
     * PUT /ui-components/{id}
     *
     * @param  mixed  $id
     * @return Response
     */
    public function update($id)
    {
        return $this->processor->update($this, $id, Input::all());
    }

    /**
     * Executes view of ui component form builder
     * 
     * @param numeric $id
     */
    public function showComponentUpdater($id, array $data)
    {
        set_meta('title', trans('Update ui component'));
        return view('antares/ui-components::admin.update.update', $data);
    }

    /**
     * Executes when validation of ui component forms failed
     */
    public function whenValidationFailed($errors)
    {
        return $this->redirectWithErrors(handles("antares::ui-components/edit"), $errors);
    }

    /**
     * When ui component params update error
     */
    public function whenUpdateError(array $errors)
    {
        $message = trans('Ui component has not been update. Please try again with other parameters or just contact with your software provider.', $errors);
        return $this->redirectWithMessage(handles("antares::ui-components/edit"), $message, 'error');
    }

    /**
     * When ui component params updates successfully
     */
    public function whenUpdateSucceed()
    {
        $message = trans('Ui component has been updated.');
        return $this->redirectWithMessage(handles("antares::ui-components"), $message);
    }

}
