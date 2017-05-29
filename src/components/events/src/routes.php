<?php
use Illuminate\Routing\Router;

$router->group(['prefix' => 'events'], function (Router $router) {
    $router->any('index', 'EventsController@index');
});