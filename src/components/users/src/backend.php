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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


use Illuminate\Routing\Router;

$router->group(['prefix' => 'account'], function (Router $router) {
    $router->get('/', 'Account\ProfileUpdaterController@edit');
    $router->post('/', 'Account\ProfileUpdaterController@update');
    $router->get('password', 'Account\PasswordUpdaterController@edit');
    $router->post('password', 'Account\PasswordUpdaterController@update');
    $router->post('picture', 'UsersController@picture');
    $router->get('gravatar', 'UsersController@gravatar');
});


$router->match(['GET', 'POST'], 'users/index', 'UsersController@index');
$router->get('users/elements', 'UsersController@elements');
$router->post('users/delete', 'UsersController@delete');
$router->match(['GET', 'POST'], 'users/{id}/status', 'UsersController@status');
$router->match(['GET', 'POST'], 'users/status', 'UsersController@status');
$router->resource('users', 'UsersController');


$router->get('login/with/{id}', '\Antares\Users\Http\Controllers\LoginAs\AuthController@login');
$router->get('logout/with/{key}', '\Antares\Users\Http\Controllers\LoginAs\AuthController@logout');
