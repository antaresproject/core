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

namespace Antares\Translations\Http\Handlers;

use Antares\Contracts\Authorization\Authorization;
use Antares\Foundation\Support\MenuHandler;
use Antares\Translations\Models\Languages;

class TranslationsBreadcrumbMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'translations-breadcrumb',
        'title' => 'Translations',
        'link'  => 'antares::translations',
        'icon'  => null,
        'boot'  => [
            'group' => 'menu.top.translations',
            'on'    => 'antares/translations::admin.translation.index'
        ]
    ];

    /**
     * Check authorization to display the menu.
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return app('antares.acl')->make('antares/translations')->can('translations-list');
    }

    /**
     * Metoda wyzwalna podczas renderowania widoku i dodająca budująca menu jako submenu breadcrumbs
     * 
     * @return void
     */
    public function handle()
    {
        if (!$this->passesAuthorization()) {
            return;
        }
        $code    = from_route('code');
        $id      = from_route('id');
        $locale  = is_null($code) ? app()->getLocale() : $code;
        $locale  = $locale == null ? 'en' : $locale;
        $current = Languages::where('code', $locale)->first();
        $this->createMenu();

        $acl = app('antares.acl')->make('antares/translations');

        if ($acl->can('add-language')) {
            $this->handler
                    ->add('translations-add-language', '^:translations-breadcrumb')
                    ->title(trans('antares/translations::messages.manage_languages'))
                    ->icon('zmdi-format-list-bulleted')
                    ->link(handles('antares::translations/languages/index'));
        }
        if ($acl->can('import-translations')) {
            $this->handler
                    ->add('translations-import', '^:translations-breadcrumb')
                    ->title(trans('antares/translations::messages.import'))
                    ->icon('zmdi-arrow-left-bottom')
                    ->link(handles('antares::translations/languages/import/' . $current->code . '/' . $id));
        }
        if ($acl->can('export-translations')) {
            $this->handler
                    ->add('translations-export', '^:translations-breadcrumb')
                    ->title(trans('antares/translations::messages.export'))
                    ->icon('zmdi-arrow-right-top')
                    ->link(handles('antares::translations/languages/export/' . $current->code . '/' . $id))
                    ->attributes([
                        'class'            => 'export-translations',
                        'data-title'       => trans('antares/translations::messages.export_translations_ask'),
                        'data-description' => trans('antares/translations::messages.export_translations_description')
            ]);
        }
        if ($acl->can('publish-translations')) {
            $this->handler
                    ->add('translations-publish', '^:translations-breadcrumb')
                    ->title(trans('antares/translations::messages.publish'))
                    ->icon('zmdi-check-all')
                    ->link(handles('antares::translations/languages/publish/' . $id))
                    ->attributes([
                        'class'            => 'publish-translations',
                        'data-title'       => trans('antares/translations::messages.publish_translations_ask'),
                        'data-description' => trans('antares/translations::messages.publish_translations_description')
            ]);
        }
    }

}
