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


namespace Antares\Notifier;

use Illuminate\Support\Manager;
use Antares\Notifier\Handlers\Laravel;
use Antares\Notifier\Handlers\Antares;

class NotifierManager extends Manager
{

    /**
     * Create Laravel driver.
     *
     * @return \Antares\Contracts\Notification\Notification
     */
    protected function createLaravelDriver()
    {
        return new Laravel($this->app->make('mailer'));
    }

    /**
     * Create Antares driver.
     *
     * @return \Antares\Contracts\Notification\Notification
     */
    protected function createAntaresDriver()
    {
        $notifier = new Antares($this->app->make('antares.notifier.email'));
        $notifier->attach($this->app->make('antares.memory')->makeOrFallback());


        return $notifier;
    }

    /**
     * Get the default driver.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app->make('config')->get('antares/notifier::driver', 'laravel');
    }

    /**
     * Set the default driver.
     *
     * @param  string  $name
     *
     * @return string
     */
    public function setDefaultDriver($name)
    {
        $this->app->make('config')->set('antares/notifier::driver', $name);
    }

}
