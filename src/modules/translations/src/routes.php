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
use Illuminate\Routing\Router;

$router->group(['prefix' => 'translations'], function (Router $router) {

    $router->get('index', 'SyncController@index');
    $router->get('sync', 'SyncController@index');
    $router->get('sync/{id}', 'SyncController@index');
    $router->get('sync/{id}/{locale}', 'SyncController@index');
    $router->any('index/{id}', 'TranslationController@index');
    $router->any('index/{id}/{code}', 'TranslationController@index')->where(['code' => '[a-z]{2}']);
    $router->any('index/{id}/{group}/{code?}', 'TranslationController@group')->where(['group' => '[A-Za-z_]+', 'code' => '[a-z]{2}']);

    $router->post('translation/{type}', 'TranslationController@translation');
    $router->post('translation/update/{type}', 'TranslationController@update');

    $router->post('update-key/{type}', 'TranslationController@updateKey');
    $router->post('update-translation/{type}', 'TranslationController@updateTranslation');
    $router->post('delete-translation/{type}', 'TranslationController@deleteTranslation');
    $router->post('add-translation/{type}/{code}', 'TranslationController@addTranslation');


    $router->get('languages', 'LanguageController@index');
    $router->match(['GET', 'POST'], 'languages/add', 'LanguageController@create');

    $router->get('languages/publish/{type}', 'LanguageController@publish');
    $router->get('languages/change/{locale}', 'LanguageController@change');

    $router->get('languages/export/{locale}/{type}', 'LanguageController@export');
    $router->match(['GET', 'POST'], 'languages/import/{locale}/{type}', 'LanguageController@import');

    $router->get('languages/delete/{code}', 'LanguageController@delete');
    $router->match(['GET', 'POST'], 'languages/index', 'LanguageController@index');
    $router->get('languages/default/{id}', 'LanguageController@setDefault');
    $router->resource('languages', 'LanguageController');
});
