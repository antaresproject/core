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
use Antares\Installation\Http\Middleware\InstallationMiddleware;

$router->group(['prefix' => 'install', 'middleware' => InstallationMiddleware::class], function (Router $router) {
    $router->get('/', 'InstallerController@index')->name('installation.installer.index');
    $router->get('create', 'InstallerController@create')->name('installation.installer.create');
    $router->post('create', 'InstallerController@store')->name('installation.installer.store');
    $router->get('done', 'InstallerController@done')->name('installation.installer.done');
    $router->get('prepare', 'InstallerController@prepare')->name('installation.installer.prepare');

    $router->get('completed', 'InstallerController@completed')->name('installation.installer.completed');
    $router->get('failed', 'InstallerController@failed')->name('installation.installer.failed');

    $router->get('progress', 'ProgressController@index')->name('installation.installer.progress.index');
    $router->get('progress/preview', 'ProgressController@preview')->name('installation.installer.progress.preview');
    $router->get('progress/stop', 'ProgressController@stop')->name('installation.installer.progress.stop');
});
