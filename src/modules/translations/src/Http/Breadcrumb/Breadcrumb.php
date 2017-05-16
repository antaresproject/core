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



namespace Antares\Translations\Http\Breadcrumb;

use Antares\Breadcrumb\Navigation;

class Breadcrumb extends Navigation
{

    /**
     * on translations list
     */
    public function onTranslationList()
    {
        $this->breadcrumbs->register('translations', function($breadcrumbs) {
            $breadcrumbs->push('Translations', handles('antares::translations/index/' . area()));
        });

        $this->shareOnView('translations');
    }

    /**
     * on language add
     */
    public function onLanguageAdd()
    {
        $this->onTranslationList();
        $this->breadcrumbs->register('languages', function($breadcrumbs) {
            $breadcrumbs->parent('translations');
            $breadcrumbs->push('Languages', handles('antares::translations/languages/index'));
        });
        $this->breadcrumbs->register('language-add', function($breadcrumbs) {
            $breadcrumbs->parent('languages');
            $breadcrumbs->push('Language add');
        });
        $this->shareOnView('language-add');
    }

    /**
     * on import translations
     */
    public function onImportTranslations()
    {
        $this->onTranslationList();
        $this->breadcrumbs->register('translations-import', function($breadcrumbs) {
            $breadcrumbs->parent('translations');
            $breadcrumbs->push('Import translations');
        });
        $this->shareOnView('translations-import');
    }

    /**
     * On languages list
     */
    public function onLanguagesList()
    {
        $this->breadcrumbs->register('translations', function($breadcrumbs) {
            $breadcrumbs->push('Translations', handles('antares::translations/index/' . area()), ['force_link' => true]);
        });

        $this->shareOnView('translations');
    }

}
