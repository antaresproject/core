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


namespace Antares\Foundation\Processor;

use Antares\Foundation\Http\Controllers\MailController as Listener;
use Antares\Foundation\Validation\Mail as Validator;
use Antares\Foundation\Http\Breadcrumb\Breadcrumb;
use Antares\Foundation\Http\Form\Mail as MailForm;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Fluent;

class Mail
{

    /**
     * breadcrumb instance
     *
     * @var Breadcrumb 
     */
    protected $breadcrumb;

    /**
     * Create a new processor instance.
     * 
     * @param Validator $validator
     * @param \Antares\Foundation\Http\Breadcrumb\Breadcrumb $breadcrumb
     */
    public function __construct(Validator $validator, Breadcrumb $breadcrumb)
    {
        $this->validator  = $validator;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * View setting page.
     *
     * @param  \Antares\Contracts\Foundation\Listener\SettingUpdater  $listener
     *
     * @return mixed
     */
    public function index()
    {
        $this->breadcrumb->onMailConfiguration();
        $memory   = app('antares.memory')->make('primary');
        $eloquent = new Fluent([
            'site_name'        => $memory->get('site.name', ''),
            'site_description' => $memory->get('site.description', ''),
            'site_registrable' => ($memory->get('site.registrable', false) ? 'yes' : 'no'),
            'mode'             => $memory->get('site.mode', 'development'),
            'email_driver'     => $memory->get('email.driver', ''),
            'email_address'    => $memory->get('email.from.address', ''),
            'email_host'       => $memory->get('email.host', ''),
            'email_port'       => $memory->get('email.port', ''),
            'email_username'   => $memory->get('email.username', ''),
            'email_password'   => $memory->get('email.password', ''),
            'email_encryption' => $memory->get('email.encryption', ''),
            'email_sendmail'   => $memory->get('email.sendmail', ''),
            'email_queue'      => ($memory->get('email.queue', false) ? 'yes' : 'no'),
            'email_key'        => $memory->get('email.key', ''),
            'email_secret'     => $memory->get('email.secret', ''),
            'email_domain'     => $memory->get('email.domain', ''),
            'email_region'     => $memory->get('email.region', ''),
        ]);
        $form     = new MailForm($eloquent);
        return compact('eloquent', 'form');
    }

    /**
     * Update setting.
     *
     * @param  Listener  $listener
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(Listener $listener, array $input)
    {
        $input      = new Fluent($input);
        $driver     = $this->getValue($input['email_driver'], 'mail.driver');
        $validation = $this->validator->on($driver)->with($input->toArray());
        if ($validation->fails()) {
            return $listener->settingFailedValidation($validation->getMessageBag());
        }

        $memory = app('antares.memory')->make('primary');
        $memory->put('email.driver', $driver);
        $memory->put('email.from', [
            'address' => $this->getValue($input['email_address'], 'mail.from.address'),
            'name'    => $input['site_name'],
        ]);
        if ((empty($input['email_password']) && $input['enable_change_password'] === 'no')) {
            $input['email_password'] = $memory->get('email.password');
        }
        if ((empty($input['email_secret']) && $input['enable_change_secret'] === 'no')) {
            $input['email_secret'] = $memory->get('email.secret');
        }

        $memory->put('email.host', $this->getValue($input['email_host'], 'mail.host'));
        $memory->put('email.port', $this->getValue($input['email_port'], 'mail.port'));
        $memory->put('email.username', $this->getValue($input['email_username'], 'mail.username'));
        $memory->put('email.password', $this->getValue($input['email_password'], 'mail.password'));
        $memory->put('email.encryption', $this->getValue($input['email_encryption'], 'mail.encryption'));
        $memory->put('email.sendmail', $this->getValue($input['email_sendmail'], 'mail.sendmail'));
        $memory->put('email.queue', ($input['email_queue'] === 'yes'));
        $memory->put('email.key', $this->getValue($input['email_key'], "services.{$driver}.key"));
        $memory->put('email.secret', $this->getValue($input['email_secret'], "services.{$driver}.secret"));
        $memory->put('email.domain', $this->getValue($input['email_domain'], "services.{$driver}.domain"));
        $memory->put('email.region', $this->getValue($input['email_region'], "services.{$driver}.region"));
        $memory->finish();

        return $listener->settingHasUpdated();
    }

    /**
     * Resolve value or grab from configuration.
     *
     * @param  mixed   $input
     * @param  string  $alternative
     *
     * @return mixed
     */
    private function getValue($input, $alternative)
    {
        if (empty($input)) {
            $input = Config::get($alternative);
        }
        return $input;
    }

}
