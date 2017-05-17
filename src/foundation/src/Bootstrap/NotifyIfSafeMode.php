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


namespace Antares\Foundation\Bootstrap;

use Antares\Contracts\Messages\MessageBag;
use Illuminate\Contracts\Foundation\Application;

class NotifyIfSafeMode
{

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     *
     * @return void
     */
    public function bootstrap(Application $app)
    {
        if ($app->make('antares.extension.mode')->check()) {
            $app->make('antares.messages')->extend(function (MessageBag $messages) {
                $messages->add('info', trans('antares/foundation::response.safe-mode'));
            });
        }
    }

}
