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



namespace Antares\Translations\Http\Presenters;

use Antares\Translations\Contracts\LanguagePresenter as PresenterContract;
use Antares\Translations\Http\Form\Language as LanguageForm;
use Antares\Translations\Http\Form\Import as ImportForm;
use Antares\Translations\Http\Breadcrumb\Breadcrumb;
use Antares\Html\Form\FormBuilder;

class LanguagePresenter implements PresenterContract
{

    /**
     * Breadcrumb instance
     * 
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * constructing
     * 
     * @param Breadcrumb $breadcrumb
     */
    public function __construct(Breadcrumb $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * create new language form
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->breadcrumb->onLanguageAdd();
        $form = $this->form();
        return view('antares/translations::admin.language.create', compact('form'));
    }

    /**
     * create from instance
     * 
     * @return Antares\Html\Form\FormBuilder
     */
    public function form()
    {
        return new FormBuilder(new LanguageForm());
    }

    /**
     * process import languages from file
     * 
     * @return \Illuminate\View\View
     */
    public function import()
    {
        $this->breadcrumb->onImportTranslations();
        $form = $this->importForm();
        return view('antares/translations::admin.language.import', compact('form'));
    }

    public function importForm()
    {
        return new FormBuilder(new ImportForm());
    }

}
