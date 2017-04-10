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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

use Illuminate\Routing\Router;

Foundation::namespaced('Antares\Foundation\Http\Controllers', function (Router $router) {

    $router->match(['GET', 'POST'], 'extensions', 'Extension\ViewerController@index')->name('extensions.index');
    $router->get('extensions/{vendor}/{name}/install', 'Extension\ActionController@install')->name('extensions.install');
    $router->get('extensions/{vendor}/{name}/activate', 'Extension\ActionController@activate')->name('extensions.activate');
    $router->get('extensions/{vendor}/{name}/deactivate', 'Extension\ActionController@deactivate')->name('extensions.deactivate');
    $router->get('extensions/{vendor}/{name}/uninstall', 'Extension\ActionController@uninstall')->name('extensions.uninstall');

    $router->get('extensions/progress', 'Extension\ProgressController@index')->name('extensions.progress.index');
    $router->get('extensions/progress/preview', 'Extension\ProgressController@preview')->name('extensions.progress.preview');

    $router->get('extensions/{vendor}/{name}/configuration', 'Extension\ViewerController@getConfiguration')->name('extensions.viewer.configuration.get');
    $router->post('extensions/{id}/configuration', 'Extension\ViewerController@storeConfiguration')->name('extensions.viewer.configuration.update');

    $router->get('extensions/debug', function() {
        dd( \Session::get('installation') );
    });

    $router->get('settings/index', 'SettingsController@edit');
    $router->post('settings/index', 'SettingsController@update');
    $router->get('settings/migrate', 'SettingsController@migrate');
    $router->resource('settings/security', 'SecurityController');

    $router->get('settings/mail', 'MailController@index');
    $router->match(['POST', 'PUT'], 'settings/mail', 'MailController@update');

    $router->match(['GET', 'HEAD'], '/', 'DashboardController@show')->name('antares.dashboard');

    $router->any('missing', 'DashboardController@missing')->name('antares.404');
    $router->get('not-allowed', 'DashboardController@notAllowed')->name('antares.not-allowed');


    $router->post('datatables/filters/store', 'Datatables\FilterController@store');
    $router->post('datatables/filters/save', 'Datatables\FilterController@save');
    $router->post('datatables/filters/update', 'Datatables\FilterController@update');
    $router->post('datatables/filters/destroy', 'Datatables\FilterController@destroy');
    $router->post('datatables/filters/delete', 'Datatables\FilterController@delete');
    $router->post('error', 'DashboardController@error');
});
