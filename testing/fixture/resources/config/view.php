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


return [

    /*
      |--------------------------------------------------------------------------
      | View Storage Paths
      |--------------------------------------------------------------------------
      |
      | Most templating systems load templates from disk. Here you may specify
      | an array of paths that should be checked for your views. Of course
      | the usual Laravel view path has already been registered for you.
      |
     */

    'paths'    => [getcwd() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views'],
    /*
      |--------------------------------------------------------------------------
      | Compiled View Path
      |--------------------------------------------------------------------------
      |
      | This option determines where all the compiled Blade templates will be
      | stored for your application. Typically, this is within the storage
      | directory. However, as usual, you are free to change this value.
      |
     */
    'compiled' => realpath(storage_path('framework/views')),
];
