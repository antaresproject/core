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

class LanguagesBreadcrumbMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'languages-breadcrumb',
        'title' => 'Languages',
        'link'  => 'antares::languages/index',
        'icon'  => null,
        'boot'  => [
            'group' => 'menu.top.languages',
            'on'    => 'antares/translations::admin.language.index'
        ]
    ];

    /**
     * Check authorization to display the menu.
     * 
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return app('antares.acl')->make('antares/translations')->can('add-language');
    }

    /**
     * Handle menu
     * 
     * @return void
     */
    public function handle()
    {
        if (!$this->passesAuthorization()) {
            return;
        }
        $this->createMenu();
        if (app('antares.acl')->make('antares/translations')->can('add-language')) {
            $this->handler
                    ->add('translations-add-language', '^:languages-breadcrumb')
                    ->title(trans('antares/translations::messages.add_new_language'))
                    ->icon('zmdi-plus-circle')
                    ->link(handles('antares::translations/languages/add'));
        }
    }

}
