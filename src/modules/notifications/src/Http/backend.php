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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
use Illuminate\Routing\Router;

$router->group(['prefix' => 'notifications'], function (Router $router) {
    $router->match(['GET', 'POST'], '/index/{type?}', 'IndexController@index');

    $router->get('edit/{id}', 'IndexController@edit');
    $router->get('edit/{id}/{locale}', 'IndexController@edit');
    $router->get('run/{id}', 'IndexController@run');
    $router->post('update', 'IndexController@update');
    $router->post('sendtest', 'IndexController@sendtest');
    $router->get('sendtest/{id}', 'IndexController@sendtest');
    $router->post('preview/{id?}', 'IndexController@preview');

    $router->get('changeStatus/{id}', 'IndexController@changeStatus');

    $router->get('create', 'IndexController@create');
    $router->get('create/{type}', 'IndexController@create');
    $router->get('create/{type}/type/{notificationType}', 'IndexController@create');
    $router->get('create/{type}/type/{notificationType}/{locale}', 'IndexController@create');
    $router->post('store', 'IndexController@store');

    $router->get('delete/{id}', 'IndexController@delete');

    /** sidebar * */
    $router->post('sidebar/delete', 'SidebarController@delete');
    $router->post('sidebar/read/{type?}', 'SidebarController@read');
    $router->get('sidebar/get', 'SidebarController@get');
    $router->get('sidebar/clear/{type?}', 'SidebarController@clear');

    $router->match(['GET', 'POST'], '/logs/index', 'LogsController@index');
    $router->get('/logs/{id}/delete', 'LogsController@delete');
    $router->post('/logs/delete', 'LogsController@delete');
    $router->get('/logs/preview/{id}', 'LogsController@preview');
});


