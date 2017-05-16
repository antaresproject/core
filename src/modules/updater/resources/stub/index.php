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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






/** stub * */
error_reporting(255);
ini_set('display_errors', '1');

ini_set('xdebug.max_nesting_level', 300);


/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 */
/*
  |--------------------------------------------------------------------------
  | Register The Auto Loader
  |--------------------------------------------------------------------------
  |
  | Composer provides a convenient, automatically generated class loader for
  | our application. We just need to utilize it! We'll simply require it
  | into the script here so that we don't have to worry about manual
  | loading any of our classes later on. It feels nice to relax.
  |
 */

require __DIR__ . '/../bootstrap/autoload.php';

/*
  |--------------------------------------------------------------------------
  | Turn On The Lights
  |--------------------------------------------------------------------------
  |
  | We need to illuminate PHP development, so let us turn on the lights.
  | This bootstraps the framework and gets it ready for use, then it
  | will load up this application so that we can run it and send
  | the responses back to the browser and delight our users.
  |
 */

$app = require_once __DIR__ . '/../bootstrap/app.php';


/*
  |--------------------------------------------------------------------------
  | Run The Application
  |--------------------------------------------------------------------------
  |
  | Once we have the application, we can handle the incoming request
  | through the kernel, and send the associated response back to
  | the client's browser allowing them to enjoy the creative
  | and wonderful application we have prepared for them.
  |
 */

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');


$response = $kernel->handle(
        $request  = Illuminate\Http\Request::capture()
);

$response->send();
$kernel->terminate($request, $response);

