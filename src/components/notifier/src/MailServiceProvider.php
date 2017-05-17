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

use Antares\Notifier\Events\CssInliner;
use Illuminate\Mail\Events\MessageSending;
use Antares\Support\Providers\Traits\EventProviderTrait;
use Illuminate\Mail\MailServiceProvider as ServiceProvider;

class MailServiceProvider extends ServiceProvider
{

    use EventProviderTrait;

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MessageSending::class => [CssInliner::class],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerEventListeners($this->app->make('events'));
    }

}
