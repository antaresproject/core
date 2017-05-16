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

use Antares\Control\Processor\PropertiesProcessor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Control\Contracts\Listener\Properties;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PropertiesController extends AdminController implements Properties
{

    /**
     * @var \Illuminate\Http\Request 
     */
    protected $request;

    /**
     * Setup a new controller.
     * 
     * @param PropertiesProcessor $processor
     */
    public function __construct(PropertiesProcessor $processor, Request $request)
    {
        $this->processor = $processor;
        $this->request   = $request;
        parent::__construct();
    }

    /**
     * Define the middleware.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.csrf', ['only' => 'delete']);
        $this->middleware('antares.can:antares/control::properties', ['only' => ['properties'],]);
        $this->middleware('antares.can:antares/control::properties-update', ['only' => ['update'],]);
    }

    /**
     * shows action properties
     * 
     * @param numeric $roleId
     * @param numeric $id
     * @return mixed
     */
    public function properties($roleId, $id)
    {
        return $this->processor->properties($this, $roleId, $id);
    }

    /**
     * shows action properties
     * 
     * @param array $data
     * @return \Illuminate\View\View
     */
    public function propertiesSucceed(array $data)
    {
        return view('antares/control::properties.properties', $data);
    }

    /**
     * when action has no properties
     * 
     * @return \Illuminate\View\View
     */
    public function noProperties()
    {
        return view('antares/control::properties.no-properties');
    }

    /**
     * updates resource action properties
     * 
     * @param numeric $roleId
     * @param numeric $formId
     */
    public function update($roleId, $formId)
    {
        return $this->processor->update($this, $roleId, $formId, Input::all());
    }

    /**
     * when properties has been updated
     * 
     * @param numeric $roleId
     * @param numeric $formId
     * @return \Illuminate\Http\Response | Redirect
     */
    public function updateSuccess($roleId, $formId)
    {
        $message = trans('Configuration has been saved.');
        return $this->response($message, $roleId, $formId);
    }

    /**
     * when properties has not been updated
     * 
     * @param numeric $roleId
     * @param numeric $formId
     * @return \Illuminate\Http\Response | Redirect
     */
    public function updateError($roleId, $formId)
    {
        $message = trans('Configuration has not been saved.');
        return $this->response($message, $roleId, $formId, true);
    }

    /**
     * default response
     * 
     * @param String $message
     * @param numeric $roleId
     * @param numeric $formId
     * @param boolean $hasError
     * @return \Illuminate\Http\Response | Redirect
     */
    protected function response($message, $roleId, $formId, $hasError = false)
    {
        return ($this->request->ajax()) ?
                Response::json(['message' => $message], ($hasError) ? 301 : 200) :
                $this->redirectWithMessage(route('control.properties.update', ['roleId' => $roleId, 'formId' => $formId], false), $message);
    }

}
