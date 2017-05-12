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

$router->match(['GET', 'POST'], 'branding/email', 'TemplateController@update');
$router->match(['GET', 'POST'], 'brands/{brands}/email', 'TemplateController@update');
$router->get('brands/{brands}/edit', 'IndexController@edit');
$router->get('branding', 'IndexController@edit');
$router->match(['GET', 'POST'], 'branding/area/{templateId}', 'IndexController@area');
$router->match(['GET', 'POST'], 'brands/{brands}/area/{templateId}', 'IndexController@area');
$router->resource('brands', 'IndexController');

$router->group(['prefix' => 'brands'], function (Router $router) {
    $router->post('upload', 'IndexController@upload');
});
