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



return [
    'di'             => [
        'Antares\Translations\Contracts\SyncPresenter'        => 'Antares\Translations\Http\Presenters\SyncPresenter',
        'Antares\Translations\Contracts\TranslationPresenter' => 'Antares\Translations\Http\Presenters\TranslationPresenter',
        'Antares\Translations\Contracts\LanguagePresenter'    => 'Antares\Translations\Http\Presenters\LanguagePresenter'
    ],
    'route'          => [
        'prefix'     => 'translations',
        'middleware' => 'auth',
    ],
    /**
     * Enable deletion of translations
     *
     * @type boolean
     */
    'delete_enabled' => true,
    /**
     * Exclude specific groups from Laravel Translation Manager. 
     * This is useful if, for example, you want to avoid editing the official Laravel language files.
     *
     * @type array
     *
     * 	array(
     * 		'pagination',
     * 		'reminders',
     * 		'validation',
     * 	)
     */
    'exclude_groups' => [],
    'export'         => [
        'separator' => ';'
    ]
];
