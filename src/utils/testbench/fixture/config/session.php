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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


return [

    

    'driver' => env('SESSION_DRIVER', 'array'),

    

    
    
    

    'encrypt' => false,

    

    'files' => storage_path('framework/sessions'),

    

    'connection' => null,

    

    'table' => 'sessions',

    

    'lottery' => [2, 100],

    

    'cookie' => 'laravel_session',

    

    'path' => '/',

    

    'domain' => null,

    

    'secure' => false,

];
