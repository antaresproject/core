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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



use Illuminate\Routing\Router;

$router->group(['prefix' => 'automation'], function (Router $router) {
    $router->match(['GET', 'POST'], 'index', 'IndexController@index');
    $router->match(['GET', 'POST'], 'show/{id}', 'IndexController@show');
    $router->get('edit/{id}', 'IndexController@edit');
    $router->get('run/{id}', 'IndexController@run');
    $router->post('update', 'IndexController@update');
    $router->get('scripts', 'IndexController@scripts');
    $router->get('logs/download', 'IndexController@download');
    $router->post('logs/delete', 'IndexController@delete');
});
$router->match(['GET', 'POST'], 'automations/logs/index', 'IndexController@logs');
