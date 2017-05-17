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
        $this->registerMailer();

        $this->registerNotifier();

        $this->registerSms();

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

            //$this->registerSwiftMailer();
            // Once we have create the mailer instance, we will set a container instance
            // on the mailer. This allows us to resolve mailer classes via containers
            // for maximum testability on said classes instead of passing Closures.
            $mailer = new Mail\Mailer(
                    $app->make('view'), $app->make('swift.mailer'), $app->make('events')
            );
            $this->setMailerDependencies($mailer, $app);

            // If a "from" address is set, we will set it on the mailer so that all mail
            // messages sent by the applications will utilize the same "from" address
            // on each one, which makes the developer's life a lot more convenient.
            $from = $app->make('config')->get('mail.from');

            if (is_array($from) && isset($from['address'])) {
                $mailer->alwaysFrom($from['address'], $from['name']);
            }

            $to = $app->make('config')->get('mail.to');

            if (is_array($to) && isset($to['address'])) {
                $mailer->alwaysTo($to['address'], $to['name']);
            }

            return $mailer;
        });
    }

    /**
     * Set a few dependencies on the mailer instance.
     *
     * @param  \Illuminate\Mail\Mailer  $mailer
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function setMailerDependencies($mailer, $app)
    {
        if ($app->bound('queue')) {
            $mailer->setQueue($app->make('queue'));
        }
    }

    /**
     * Register the Swift Mailer instance.
     *
     * @return void
     */
    public function registerSwiftMailer()
    {
        $this->registerSwiftTransport();

        // Once we have the transporter registered, we will register the actual Swift
        // mailer instance, passing in the transport instances, which allows us to
        // override this transporter instances during app start-up if necessary.
        $this->app['swift.mailer'] = $this->app->share(function ($app) {
            return new Swift_Mailer($app->make('swift.transport')->driver());
        });
    }

    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    protected function registerSwiftTransport()
    {
        $this->app['swift.transport'] = $this->app->share(function ($app) {
            $transportManager = new TransportManager($app);
            $transportManager->attach(app('antares.memory')->make('primary'));
            return $transportManager;
        });
    }

    /**
     * Register the service provider for mail.
     *
     * @return void
     */
    protected function registerMailer()
    {
        $this->app->singleton('antares.notifier.email', function ($app) {
            $transport = new TransportManager($app);
            $transport->attach(app('antares.memory')->make('primary'));
            return new Mailer($app, $transport);
        });
    }

    /**
     * Register the service provider for notifier.
     *
     * @return void
     */
    protected function registerNotifier()
    {
        $this->app->singleton('antares.notifier', function ($app) {
            return new NotifierManager($app);
        });
    }

    /**
     * Register the service provider for notifier.
     *
     * @return void
     */
    protected function registerSms()
    {
        $this->app->singleton('antares.notifier.sms', function ($app) {
            return new SmsManager($app);
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
        return ['antares.notifier.sms', 'antares.notifier.email', 'antares.notifier'];
    }

}
