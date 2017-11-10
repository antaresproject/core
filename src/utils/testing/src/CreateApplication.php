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

namespace Antares\Testing;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     * @throws \Exception
     */
    public function createApplication()
    {
        $defaultPath    = __DIR__.'/../../../../../../bootstrap/app.php';
        $path           = __DIR__ . env('APP_BOOTSTRAP_FILE', $defaultPath);

        if( ! file_exists($path)) {
            throw new \Exception('File [' . $path . '] does not exist.');
        }

        $app = require $path;
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}