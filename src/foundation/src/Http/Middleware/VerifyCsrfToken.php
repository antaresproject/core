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


namespace Antares\Foundation\Http\Middleware;

use Antares\Http\Traits\PassThroughTrait;
use Illuminate\Contracts\Encryption\Encrypter;
use Antares\Contracts\Foundation\Foundation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{

    use PassThroughTrait;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Encryption\Encrypter  $encrypter
     * @param  \Antares\Contracts\Foundation\Foundation  $foundation
     */
    public function __construct(Application $app, Encrypter $encrypter, Foundation $foundation)
    {
        $this->foundation = $foundation;

        parent::__construct($app, $encrypter);
    }

}
