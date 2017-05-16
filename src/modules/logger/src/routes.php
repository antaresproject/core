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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
use Illuminate\Routing\Router;

$router->get('download/{path}', 'DownloadController@download');

$router->group(['prefix' => 'logger'], function (Router $router) {

    $router->match(['GET', 'POST'], 'devices/index', 'DevicesController@index');
    $router->resource('devices', 'DevicesController');


    $router->get('index', 'IndexController@system');
    $router->any('system/index', 'IndexController@system');
    $router->get('delete/{date}', 'IndexController@delete');
    $router->get('download/{date}', 'IndexController@download');
    $router->match(['GET', 'POST'], 'details/{date}/{type?}', 'IndexController@details');
    $router->get('modules', 'ModulesController@index');
    $router->get('information/index', 'SystemController@index');
    $router->get('error', 'SystemController@error');

    $router->match(['GET', 'POST'], 'request/index', 'RequestController@index');
    $router->match(['GET', 'POST'], 'request/show/{date}', 'RequestController@show');
    $router->get('request/clear/{date}', 'RequestController@clear');
    $router->get('request/download/{date}', 'RequestController@download');
    $router->any('activity/index', 'ActivityController@index');
    $router->any('activity/index/type/{typeId}', 'ActivityController@index');
    $router->match(['GET', 'POST'], 'activity/delete/{id?}', 'ActivityController@delete');
    $router->get('activity/show/{id}', 'ActivityController@show');
    $router->get('activity/download', 'ActivityController@download');

    $router->get('report', 'ReportController@send');
    $router->post('report', 'ReportController@send');

    $router->get('history', 'HistoryController@index');
    $router->get('history/delete/{id}', 'HistoryController@delete');
    $router->get('history/show/{id}', 'HistoryController@show');

    $router->post('analyze', 'AnalyzeController@index');
    $router->any('analyze/{action}', 'AnalyzeController@run');

    $router->post('generate', 'GeneratorController@generate');
    $router->get('view/{id}', 'GeneratorController@view');
    $router->get('view/{id}/html', 'GeneratorController@html');
    $router->get('download/{type}/{id}', 'GeneratorController@download');

    $router->get('generate/standalone', 'GeneratorController@generateStandalone');
    $router->get('generate/download/{filename}', 'GeneratorController@downloadReport');
});
