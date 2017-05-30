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

use Illuminate\Contracts\Mail\Mailer as Mail;
use SuperClosure\SerializableClosure;
use Illuminate\Contracts\Queue\Job;
use Antares\Memory\ContainerTrait;
use Swift_Mailer;
use Exception;
use Closure;

class Mailer
{

    use ContainerTrait;

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Mailer instance.
     *
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mailer;

    /**
     * Transporter instance.
     *
     * @var \Antares\Notifier\TransportManager
     */
    protected $transport;

    /**
     * Result message
     *
     * @var String 
     */
    protected $message;

    /**
     * Result code
     *
     * @var mixed
     */
    protected $code;

    /**
     * Construct a new Mail instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @param  \Antares\Notifier\TransportManager  $transport
     */
    public function __construct($app, TransportManager $transport)
    {
        $this->app       = $app;
        $this->transport = $transport;
    }

    /**
     * Register the Swift Mailer instance.
     *
     * @return \Illuminate\Contracts\Mail\Mailer
     */
    public function getMailer()
    {
        if (!$this->mailer instanceof Mail) {
            $this->transport->setMemoryProvider($this->memory);
            $this->mailer = $this->resolveMailer();
        }

        return $this->mailer;
    }

    /**
     * Allow Antares to either use send or queue based on
     * settings.
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @param  string  $queue
     *
     * @return \Antares\Contracts\Notification\Receipt
     */
    public function push($view, array $data, $callback, $queue = null)
    {
        $method = 'queue';
        $memory = $this->memory;

        if (false === $memory->get('email.queue', false)) {
            $method = 'send';
        }

        return call_user_func([$this, $method], $view, $data, $callback, $queue);
    }

    /**
     * Force Antares to send email directly.
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     *
     * @return \Antares\Contracts\Notification\Receipt
     */
    public function send($view, array $data, $callback)
    {
        $mailer = $this->getMailer();
        event('antares.notifier.before_send_email', [&$view, $data]);
        try {
            $this->code    = $mailer->send($view, $data, $callback);
            $this->message = ($this->code) > 0 ? trans('antares/foundation::messages.notifier_mail_has_been_sent') : trans('antares/foundation::messages.notifier_mail_has_not_been_sent');
        } catch (Exception $ex) {
            \Illuminate\Support\Facades\Log::error($ex);
            $this->code    = $ex->getCode();
            $this->message = $ex->getMessage();
        }

        return $this;
    }

    /**
     * Result message getter
     * 
     * @return String
     */
    public function getResultMessage()
    {
        return $this->message;
    }

    /**
     * Result code getter
     * 
     * @return mixed
     */
    public function getResultCode()
    {
        return $this->code;
    }

    /**
     * Force Antares to send email using queue.
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @param  string  $queue
     *
     * @return \Antares\Contracts\Notification\Receipt
     */
    public function queue($view, array $data, $callback, $queue = null)
    {
        $callback = $this->buildQueueCallable($callback);

        $with = [
            'view'     => $view,
            'data'     => $data,
            'callback' => $callback,
        ];

        $this->app->make('queue')->push('antares.mail@handleQueuedMessage', $with, $queue);

        return new Receipt($this->mailer ?: $this->app->make('mailer'), true);
    }

    /**
     * Build the callable for a queued e-mail job.
     *
     * @param  mixed  $callback
     *
     * @return mixed
     */
    protected function buildQueueCallable($callback)
    {
        if (!$callback instanceof Closure) {
            return $callback;
        }

        return serialize(new SerializableClosure($callback));
    }

    /**
     * Handle a queued e-mail message job.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  array  $data
     *
     * @return void
     */
    public function handleQueuedMessage(Job $job, $data)
    {
        $this->send($data['view'], $data['data'], $this->getQueuedCallable($data));

        $job->delete();
    }

    /**
     * Get the true callable for a queued e-mail message.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    protected function getQueuedCallable(array $data)
    {
        if (str_contains($data['callback'], 'SerializableClosure')) {
            return with(unserialize($data['callback']))->getClosure();
        }

        return $data['callback'];
    }

    /**
     * Setup mailer.
     *
     * @return \Illuminate\Contracts\Mail\Mailer
     */
    protected function resolveMailer()
    {
        $from   = $this->memory->get('email.from');
        $mailer = $this->app->make('antares.support.mail');

        if (is_array($from) && !empty($from['address'])) {
            $mailer->alwaysFrom($from['address'], $from['name']);
        }
        $mailer->setSwiftMailer(new Swift_Mailer($this->transport->driver()));

        return $mailer;
    }

}
