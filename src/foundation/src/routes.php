<?php

/**
 * Part of the Antares package.
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
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
use Illuminate\Routing\Router;

Foundation::namespaced('Antares\Foundation\Http\Controllers', function (Router $router) {

    $router->match(['GET', 'POST'], 'modules', 'Extension\ViewerController@index')->name('modules.index');
    $router->get('modules/{vendor}/{name}/install', 'Extension\ActionController@install')->name('modules.install');
    $router->get('modules/{vendor}/{name}/activate', 'Extension\ActionController@activate')->name('modules.activate');
    $router->get('modules/{vendor}/{name}/deactivate', 'Extension\ActionController@deactivate')->name('modules.deactivate');
    $router->get('modules/{vendor}/{name}/uninstall', 'Extension\ActionController@uninstall')->name('modules.uninstall');

    $router->get('modules/progress', 'Extension\ProgressController@index')->name('modules.progress.index');
    $router->get('modules/progress/preview', 'Extension\ProgressController@preview')->name('modules.progress.preview');
    $router->get('modules/progress/stop', 'Extension\ProgressController@stop')->name('modules.progress.stop');

    $router->get('modules/{vendor}/{name}/configuration', 'Extension\ViewerController@getConfiguration')->name('modules.viewer.configuration.get');
    $router->post('modules/{id}/configuration', 'Extension\ViewerController@storeConfiguration')->name('modules.viewer.configuration.update');

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
