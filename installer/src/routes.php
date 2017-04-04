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

$router->group(['prefix' => 'install'], function (Router $router) {
    $router->get('/', 'InstallerController@index');
    $router->get('create', 'InstallerController@create');
    $router->post('create', 'InstallerController@store');
    $router->get('done', 'InstallerController@done');
    $router->get('prepare', 'InstallerController@prepare');


    $router->get('components', 'InstallerController@components');
    $router->post('components/store', 'InstallerController@storeComponents');

    $router->get('completed', 'InstallerController@completed');
    $router->get('failed', 'InstallerController@failed');
});
