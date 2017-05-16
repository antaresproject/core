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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



use Illuminate\Routing\Router;

$router->post('customfields/index', 'IndexController@index');
$router->get('customfields/index', 'IndexController@index');
$router->match(['GET', 'POST'], 'customfields/{category?}/index', 'IndexController@index');
$router->resource('customfields', 'IndexController');


$router->group(['prefix' => 'customfields'], function (Router $router) {

    $router->get('widget', 'IndexController@widget');
    $router->get('{customfield}/delete', 'IndexController@delete');
    $router->get('{customfield}/edit', 'IndexController@edit');


    $router->get('create/category/{category}', 'IndexController@create');
    $router->get('create/category/{category}/group/{group}', 'IndexController@create');
    $router->get('create/category/{category}/group/{group}/type/{type}', 'IndexController@create');


    $router->get('create', array('as' => 'customfield.create', 'uses' => 'IndexController@create'));
    $router->get('id/{customfield}/edit', array('as' => 'customfield.base', 'uses' => 'IndexController@edit'));
    $router->get('id/{customfield}/edit/category/{category}', 'IndexController@edit');
    $router->get('id/{customfield}/edit/category/{category}/group/{group}', 'IndexController@edit');
    $router->get('id/{customfield}/edit/category/{category}/group/{group}/type/{type}', 'IndexController@edit');
});
