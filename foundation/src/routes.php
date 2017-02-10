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
use Antares\Support\Facades\Foundation;

Foundation::namespaced('Antares\Foundation\Http\Controllers', function (Router $router) {

    $router->match(['GET', 'POST'], 'extensions', 'Extension\ViewerController@index');
    $router->get('extensions/{vendor}/{package}/activate', 'Extension\ActionController@activate');
    $router->get('extensions/{vendor}/activate', 'Extension\ActionController@activate');
    $router->get('extensions/{vendor}/{package}/deactivate', 'Extension\ActionController@deactivate');
    $router->get('extensions/{vendor}/deactivate', 'Extension\ActionController@deactivate');

    $router->get('extensions/{vendor}/{package}/update', 'Extension\ActionController@migrate');
    $router->get('extensions/{vendor}/update', 'Extension\ActionController@migrate');

    $router->get('extensions/{vendor}/{package}/configure', 'Extension\ConfigureController@configure');
    $router->get('extensions/{vendor}/configure', 'Extension\ConfigureController@configure');

    $router->post('extensions/{vendor}/{package}/configure', 'Extension\ConfigureController@update');
    $router->post('extensions/{vendor}/configure', 'Extension\ConfigureController@update');

    /**
     * uninstall & delete extensions
     */
    $router->get('extensions/{vendor}/{package}/delete', 'Extension\ActionController@delete');
    $router->get('extensions/{vendor}/{package}/uninstall', 'Extension\ActionController@uninstall');

    /**
     * module create form *
     */
    $router->get('modules/create', array(
        'as'   => 'modules/create',
        'uses' => 'Extension\ModuleConfigureController@create'
    ));
    $router->post('modules/create', array(
        'as'   => 'modules/create',
        'uses' => 'Extension\ModuleConfigureController@create'
    ));
    $router->get('modules/{category}/create', array(
        'as'   => 'modules/{category}/create',
        'uses' => 'Extension\ModuleConfigureController@create'
    ));
    $router->post('modules/{category}/create', array(
        'as'   => 'modules/{category}/create',
        'uses' => 'Extension\ModuleConfigureController@create'
    ));

    /**
     * module uploads *
     */
    $router->post('modules/upload', 'Extension\ModuleConfigureController@upload');
    $router->post('modules/{category}/upload', 'Extension\ModuleConfigureController@upload');

    /**
     * module list *
     */
    $router->get('modules', 'Extension\ModuleController@index');
    $router->get('modules//', 'Extension\ModuleController@index');
    $router->get('modules/{category}', 'Extension\ModuleController@index');

    /**
     * module preparation before activation *
     */
    $router->get('modules/{category}/{vendor}/{package}/{type}/prepare', 'Extension\ModuleController@prepare');
    $router->get('modules/{category}/{vendor}/{type}/prepare', 'Extension\ModuleController@prepare');

    /**
     * module activation *
     */
    $router->get('modules/{category}/{vendor}/{package}/activate', 'Extension\ModuleController@activate');
    $router->get('modules/{category}/{vendor}/activate', 'Extension\ModuleController@activate');

    /**
     * module deactivation *
     */
    $router->get('modules/{category}/{vendor}/{package}/deactivate', 'Extension\ModuleController@deactivate');
    $router->get('modules/{category}/{vendor}/deactivate', 'Extension\ModuleController@deactivate');

    /**
     * module uninstall *
     */
    $router->get('modules/{category}/{vendor}/{package}/uninstall', 'Extension\ModuleController@uninstall');
    $router->get('modules/{category}/{vendor}/uninstall', 'Extension\ModuleController@uninstall');

    /**
     * module delete *
     */
    $router->get('modules/{category}/{vendor}/{package}/delete', 'Extension\ModuleController@delete');
    $router->get('modules/{category}/{vendor}/delete', 'Extension\ModuleController@delete');

    /**
     * module configuration form *
     */
    $router->get('modules/{category}/{vendor}/{package}/update', 'Extension\ModuleController@migrate');
    $router->get('modules/{vendor}/{package}/update', 'Extension\ModuleController@migrate');
    $router->get('modules/{vendor}/update', 'Extension\ModuleController@migrate');

    /**
     * module configure *
     */
    $router->get('modules/{category}/{vendor}/{package}/configure', 'Extension\ModuleConfigureController@configure');
    $router->get('modules/{category}/{vendor}/configure', 'Extension\ModuleConfigureController@configure');
    $router->post('modules/{category}/{vendor}/{package}/configure', 'Extension\ModuleConfigureController@update');
    $router->post('modules/{category}/{vendor}/configure', 'Extension\ModuleConfigureController@update');
    $router->get('publisher', 'PublisherController@index');

    $router->get('settings/index', 'SettingsController@edit');
    $router->post('settings/index', 'SettingsController@update');
    $router->get('settings/migrate', 'SettingsController@migrate');
    $router->resource('settings/security', 'SecurityController');

    $router->get('settings/mail', 'MailController@index');
    $router->match(['POST', 'PUT'], 'settings/mail', 'MailController@update');


    $router->match(['GET', 'HEAD'], '/', ['as' => 'antares.dashboard', 'before' => 'antares.installable', 'uses' => 'DashboardController@show']);

    $router->any('missing', ['as' => 'antares.404', 'uses' => 'DashboardController@missing']);
    $router->get('not-allowed', ['as' => 'antares.not-allowed', 'uses' => 'DashboardController@notAllowed']);


    $router->post('datatables/filters/store', 'Datatables\FilterController@store');
    $router->post('datatables/filters/save', 'Datatables\FilterController@save');
    $router->post('datatables/filters/update', 'Datatables\FilterController@update');
    $router->post('datatables/filters/destroy', 'Datatables\FilterController@destroy');
    $router->post('datatables/filters/delete', 'Datatables\FilterController@delete');
    $router->post('error', 'DashboardController@error');
});
