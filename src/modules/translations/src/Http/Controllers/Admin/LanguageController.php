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

use Antares\Translations\Processor\LanguageProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Translations\Contracts\LanguageListener;
use Illuminate\Http\Request;

class LanguageController extends AdminController implements LanguageListener
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
    public function __construct(Processor $processor, Request $request)
    {
        parent::__construct();
        $this->processor = $processor;
        $this->request   = $request;
    }

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.can:antares/translations::add-language', ['only' => ['create'],]);
        $this->middleware('antares.can:antares/translations::publish-translations', ['only' => ['publish'],]);
        $this->middleware('antares.can:antares/translations::export-translations', ['only' => ['export'],]);
        $this->middleware('antares.can:antares/translations::import-translations', ['only' => ['import'],]);
        $this->middleware('antares.can:antares/translations::change-language', ['only' => ['change'],]);
    }

    /**
     * add language action
     */
    public function create()
    {
        return $this->processor->create($this, $this->request);
    }

    /**
     * Shows languages list
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->index();
    }

    /**
     * Response when storing language failed
     *
     * @param  array  $errors
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createFailed($errors = null)
    {
        app('antares.messages')->add('error', empty($errors) ? trans('Unable to add language.') : $errors);
        return redirect()->back();
    }

    /**
     * Response when storing language succeed.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createSucceed()
    {
        return $this->redirectWithMessage(handles('antares::translations/languages/index'), trans('Language has been added'));
    }

    /**
     * publish translations to files
     * 
     * @param mixed $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publish($type)
    {
        return $this->processor->publish($this, $type);
    }

    /**
     * response when publishing completed successfully
     * 
     * @param mixed $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publishSucceed($type)
    {
        app('antares.messages')->add('success', trans('Translations has been published.'));
        return redirect(handles('antares::translations/index/' . $type));
    }

    /**
     * response when publishing failed
     * 
     * @param mixed $type
     * @param String $error
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publishFailed($type, $error = null)
    {
        app('antares.messages')->add('error', is_null($error) ? trans('Translations has not been published.') : $error);
        return redirect(handles('antares::translations/index/' . $type));
    }

    /**
     * exporting translations
     * 
     * @param String $locale
     * @param String $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function export($locale, $type)
    {
        return $this->processor->export($this, $locale, $type);
    }

    /**
     * response when exporting failed
     * 
     * @param String $error
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exportFailed($error = null)
    {
        app('antares.messages')->add('error', is_null($error) ? trans('Translations has not been exported.') : $error);
        return redirect()->back();
    }

    /**
     * importing translations
     * 
     * @param String $locale
     * @param String $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import($locale, $type)
    {
        return $this->processor->import($this, $type, $locale);
    }

    /**
     * response when importing completed successfully
     * 
     * @param String $type 
     * @param String $locale 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importSuccess($type, $locale)
    {
        app('antares.messages')->add('success', trans('antares/translations::messages.translations_import_success'));
        return redirect(handles("antares::translations/index/$type/$locale"));
    }

    /**
     * response when exporting failed
     * 
     * @param String $error
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importFailed()
    {
        app('antares.messages')->add('error', trans('antares/translations::messages.translations_import_failed'));
        return redirect()->back();
    }

    /**
     * change language
     * 
     * @param String $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function change($locale)
    {
        return $this->processor->change($this, $locale);
    }

    /**
     * response when changing language completed successfully
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeSuccess()
    {
        app('antares.messages')->add('success', trans('Language has been changed.'));
        return redirect()->back();
    }

    /**
     * response when changing language failed
     * 
     * @param String $error
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeFailed($error = null)
    {
        app('antares.messages')->add('error', is_null($error) ? trans('Language has not been changed.') : $error);
        return redirect()->back();
    }

    /**
     * deleteing language
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        return $this->processor->delete($this, $id);
    }

    /**
     * response when deletion succeed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSuccess()
    {
        app('antares.messages')->add('success', trans('Language has been deleted.'));
        return redirect()->back();
    }

    /**
     * response when deletion failed
     * 
     * @param String $error
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFailed($error = null)
    {
        app('antares.messages')->add('error', $error ? $error : trans('Language has not been deleted.'));
        return redirect()->back();
    }

    /**
     * Sets default language
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setDefault($id)
    {
        return $this->processor->setDefault($this, $id);
    }

    /**
     * Response when default language has been set successfully
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function defaultSuccess()
    {
        app('antares.messages')->add('success', trans('Language has been set as default.'));
        return redirect()->back();
    }

    /**
     * Response when default language has not been set
     * 
     * @param String $error
     * @return \Illuminate\Http\RedirectResponse
     */
    public function defaultFailed($error = null)
    {
        app('antares.messages')->add('error', $error ? $error : trans('Language has not been set as default.'));
        return redirect()->back();
    }

}
