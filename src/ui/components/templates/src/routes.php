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
 * @package    Widgets
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
use Illuminate\Routing\Router;

$router->resource('widgets', 'DefaultController', ['only' => ['index', 'show']]);
$router->resource('widgets/creator/type/{type}', 'CreateController', ['only' => ['create', 'store']]);
$router->resource('widgets/creator', 'CreateController', ['only' => ['create', 'store']]);
$router->resource('widgets/updater', 'UpdateController', ['only' => ['edit', 'update']]);
$router->resource('widgets/destroyer', 'DestroyController', ['only' => ['destroy']]);

$router->group(['prefix' => 'ui-components'], function (Router $router) {

    $router->get('/', ['as' => 'view-widgets', 'uses' => 'DefaultController@index']);
    $router->match(['GET', 'POST'], 'creator/create/type/{type}', 'CreateController@create');
    $router->get('destroyer/{id}/destroy', 'DestroyController@destroy');
    $router->get('destroyer/{id}/disable', 'DestroyController@disable');
    $router->post('grid', 'DefaultController@grid');
    $router->post('view/{id}', 'DefaultController@view');
    $router->post('show/{id}', 'DefaultController@show');
    $router->get('view/{id}/page/{pageId}', 'DefaultController@view');
});
