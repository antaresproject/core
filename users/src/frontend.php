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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


use Illuminate\Routing\Router;

if (config('users.allow_registration', true)) {
    $router->group(['middleware' => ['web']], function (Router $router) {
        $router->resource('register', '\Antares\Users\Http\Controllers\Account\ProfileCreatorController');
    });
}
Foundation::namespaced('Antares\Users\Http\Controllers', function (Router $router) {
    $router->group(['prefix' => 'forgot'], function (Router $router) {
        $router->get('/', 'Account\PasswordBrokerController@create');
        $router->post('/', 'Account\PasswordBrokerController@store');
        $router->match(['GET', 'POST'], 'reset/{token}', 'Account\PasswordBrokerController@show');
        $router->post('reset', 'Account\PasswordBrokerController@update');
    });
    $router->get('login', ['as' => 'login', 'uses' => 'CredentialController@index']);
    $router->post('login', 'CredentialController@login');
    $router->match(['GET', 'HEAD', 'DELETE'], 'logout', 'CredentialController@logout');
});
Route::group(['middleware' => ['web']], function () use($router) {
    $router->get('antares/login/with/{id}', 'Antares\Users\Http\Controllers\LoginAs\AuthController@login');
    $router->get('antares/logout/with/{key}', 'Antares\Users\Http\Controllers\LoginAs\AuthController@logout');

    $router->get('login/with/{id}', 'Antares\Users\Http\Controllers\LoginAs\AuthController@login');
    $router->get('logout/with/{key}', 'Antares\Users\Http\Controllers\LoginAs\AuthController@logout');
});

