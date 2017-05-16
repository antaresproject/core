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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
use Illuminate\Routing\Router;

$router->group(['prefix' => 'control'], function (Router $router) {

    $router->get('acl', 'AuthorizationController@edit');
    $router->post('acl', 'AuthorizationController@update');

    $router->match(['GET', 'POST'], 'index/roles', 'RolesController@index');
    $router->resource('roles', 'RolesController');
    $router->match(['GET', 'HEAD', 'DELETE'], 'roles/{roles}/delete', 'RolesController@delete');
    $router->match(['GET', 'HEAD', 'DELETE'], 'roles/{roles}/acl', 'RolesController@acl');
    $router->get('acl/{id}', 'RolesController@acl');
    $router->get('tree/{id}', 'RolesController@tree');

    /**
     * users
     */
    $router->match(['GET', 'POST'], 'index/users', 'UsersController@index');
    $router->resource('users', 'UsersController');




    /**
     * form
     */
    $router->match(['GET', 'HEAD', 'DELETE'], 'properties/{roleId}/id/{formId}', 'PropertiesController@properties');
    $router->post('update/{roleId}/id/{formId}', ['as' => 'control.properties.update', 'uses' => 'PropertiesController@update']);
});
