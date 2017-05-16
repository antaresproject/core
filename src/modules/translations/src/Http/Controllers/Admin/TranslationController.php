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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Translations\Http\Controllers\Admin;

use Antares\Translations\Processor\TranslationProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Translations\Contracts\TranslationListener;
use Illuminate\Http\Request;

class TranslationController extends AdminController implements TranslationListener
{

    /**
     * Request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * implments instance of controller
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
        $this->middleware('antares.can:antares/translations::translations-list', ['only' => ['index', 'group'],]);
        $this->middleware('antares.can:antares/translations::edit-translation', ['only' => ['translation', 'update'],]);
    }

    /**
     * index default action
     * 
     * @param mixed $id
     * @param String $code
     * @return \Illuminate\View\View
     */
    public function index($id, $code = null)
    {
        return $this->processor->index($id, $code);
    }

    /**
     * change group of translations
     * 
     * @param numeric $typeId
     * @param String $group
     * @param String $code
     */
    public function group($typeId, $group = null, $code = null)
    {
        return $this->processor->group($this, $typeId, $group, $code);
    }

    /**
     * response when group has not been changed
     *
     * @param  String  $error
     * @return \Illuminate\Http\RedirectResponse
     */
    public function groupFailed($error = null)
    {
        $message = is_null($error) ? trans('Unable to change translation group.') : $error;
        app('antares.messages')->add('error', $message);
        return redirect()->back();
    }

    /**
     * single term translation 
     * 
     * @param mixed $type
     * @param String $code     
     * @return \Illuminate\View\View
     */
    public function translation($type)
    {
        return $this->processor->translation($type);
    }

    /**
     * update term translation 
     * 
     * @param mixed $type
     * @return \Illuminate\View\View
     */
    public function update($type)
    {
        return $this->processor->update($this, $type);
    }

    public function updateKey()
    {
        return $this->processor->updateKey();
    }

    public function updateTranslation()
    {
        return $this->processor->updateTranslation();
    }

    public function deleteTranslation()
    {
        return $this->processor->deleteTranslation();
    }

    public function addTranslation($type, $code)
    {
        return $this->processor->addTranslation($type, $code);
    }

    /**
     * when update completed successfully
     * 
     * @param String $type
     * @param String $code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSuccessfull($type, $code)
    {
        $message = trans('antares/translations::messages.update_success');
        return $this->redirectWithMessage(handles('antares::translations/index/' . $type . '/' . $code), $message);
    }

    /**
     * when update has failed
     * 
     * @param String $type
     * @param String $code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFailed($type, $code)
    {
        $message = trans('antares/translations::messages.update_failed');
        return $this->redirectWithMessage(handles('antares::translations/index/' . $type), $message, 'error');
    }

}
