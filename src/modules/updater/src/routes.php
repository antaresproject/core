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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
use Illuminate\Routing\Router;

$router->get('sandbox', 'SandboxController@index');

$router->group(['prefix' => 'updater'], function (Router $router) {
    $router->get('/', 'IndexController@index');
    $router->get('hide', 'IndexController@hide');
    $router->get('update', 'IndexController@update');

    $router->post('installation/start/{token}', ['middleware' => 'antares.csrf', 'as' => 'installation/start', 'uses' => 'UpdateController@start']);
    $router->get('installation/start/{token}', ['middleware' => 'antares.csrf', 'as' => 'installation/start', 'uses' => 'UpdateController@start']);

    /** backups * */
    $router->match(['GET', 'POST'], 'backups', 'BackupController@index');
    $router->get('backups/create', 'BackupController@create');
    $router->get('backups/delete/{id}', 'BackupController@delete');

    /** sandboxes * */
    $router->match(['GET', 'POST'], 'sandboxes', 'SandboxController@index');
    $router->get('sandbox/delete/{id}', 'SandboxController@delete');


    /** sandbox * */
    $router->post('sandbox/requirements', 'SandboxController@requirements');
    $router->get('sandbox/requirements', 'SandboxController@requirements');

    $router->post('sandbox/backup', 'SandboxController@backup');
    $router->get('sandbox/backup', 'SandboxController@backup');

    $router->post('sandbox/database', 'SandboxController@database');
    $router->get('sandbox/database', 'SandboxController@database');

    $router->post('sandbox/migration', 'SandboxController@migration');
    $router->get('sandbox/migration', 'SandboxController@migration');

    $router->post('sandbox/ending', 'SandboxController@ending');
    $router->get('sandbox/ending', 'SandboxController@ending');


    $router->post('sandbox/save', 'SandboxController@save');
    $router->get('sandbox/save', 'SandboxController@save');

    $router->post('sandbox/open', 'SandboxController@open');
    $router->get('sandbox/open', 'SandboxController@open');

    $router->post('sandbox/rollback', 'SandboxController@rollback');
    $router->get('sandbox/rollback', 'SandboxController@rollback');

    $router->post('sandbox/done', 'SandboxController@done');
    $router->get('sandbox/done', 'SandboxController@done');

    $router->post('installation/start', 'UpdateController@start');


    $router->post('installation/start', 'UpdateController@start');
    $router->get('installation/start', 'UpdateController@start');

    /** module update * */
    $router->get('module/update/{name}/{version}', 'ModuleController@update');
    $router->post('module/update/{name}/{version}', 'ModuleController@update');


    /** production mode routes * */
    $router->any('production/iterations', 'ProductionController@iterations');
    $router->any('production/validate', 'ProductionController@validate');
    $router->any('production/backup', 'ProductionController@backup');
    $router->any('production/database', 'ProductionController@database');
    $router->any('production/migration', 'ProductionController@migration');
    $router->any('production/finish', 'ProductionController@finish');
    $router->any('production/rollback', 'ProductionController@rollback');

    /** restoring application from backup instance * */
    $router->any('backups/restore/{id}', 'BackupController@restore');
    $router->get('error_page', 'IndexController@error');
});
