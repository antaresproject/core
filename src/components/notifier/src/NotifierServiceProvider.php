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

use Antares\Support\Providers\ServiceProvider;
use Illuminate\Mail\Mailer;
use Swift_Mailer;

class NotifierServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->registerSwiftTransport();

        $this->registerSupportedMailer();
    }

    /**
     * Register the support mailer
     *
     * @return void
     */
    protected function registerSupportedMailer()
    {
        $this->app->singleton('antares.support.mail', function ($app) {
            $this->registerSwiftMailer();

            $mailer = new Mailer(
                    $app->make('view'), $app->make('antares.swift.mailer'), $app->make('events')
            );
            if ($app->bound('queue')) {
                $mailer->setQueue($app->make('queue'));
            }
            $from   = $app->make('antares.memory')->make('primary')->get('email.from');
            $config = $app->make('config')->get('mail.from');


            $mailer->alwaysFrom(array_get($from, 'address', array_get($config, 'address')), array_get($from, 'name', array_get($config, 'name')));

            $to = $app->make('config')->get('mail.to');

            if (is_array($to) && isset($to['address'])) {
                $mailer->alwaysTo($to['address'], $to['name']);
            }

            return $mailer;
        });
    }

    /**
     * Register the Swift Mailer instance.
     *
     * @return void
     */
    public function registerSwiftMailer()
    {
        $this->app->singleton('antares.swift.mailer', function ($app) {
            return new Swift_Mailer($app->make('antares.swift.transport')->driver());
        });
    }

    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    protected function registerSwiftTransport()
    {
        $this->app->singleton('antares.swift.transport', function ($app) {
            $transportManager = new TransportManager($app);
            $transportManager->attach($app->make('antares.memory')->make('primary'));
            return $transportManager;
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ . '/../resources');
        $this->addConfigComponent('antares/notifier', 'antares/notifier', $path . '/config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['antares.support.mail'];
    }

}
