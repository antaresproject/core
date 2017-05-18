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


namespace Antares\Foundation\Validation;

use Antares\Support\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorResolver;

class Mail extends Validator
{

    /**
     * List of rules.
     *
     * @var array
     */
    protected $rules = [
        'email_address' => ['required', 'email'],
        'email_driver'  => ['required', 'in:mail,smtp,sendmail,ses,mailgun,mandrill'],
        'email_port'    => ['numeric'],
    ];

    /**
     * List of events.
     *
     * @var array
     */
    protected $events = [
        'antares.validate: mail',
    ];

    /**
     * On update email using smtp driver scenario.
     *
     * @return void
     */
    protected function onSmtp()
    {
        $this->rules['email_username'] = ['required'];
        $this->rules['email_host']     = ['required'];
    }

    /**
     * On update email using sendmail driver scenario.
     *
     * @return void
     */
    protected function onSendmail()
    {
        $this->rules['email_sendmail'] = ['required'];
    }

    /**
     * On update email using mailgun driver scenario.
     *
     * @return void
     */
    protected function onMailgun()
    {
        $this->rules['email_domain'] = ['required'];
    }

    /**
     * On update email using SES driver scenario.
     *
     * @return void
     */
    protected function onSes()
    {
        $this->rules['email_key']    = ['required'];
        $this->rules['email_region'] = ['required', 'in:us-east-1,us-west-2,eu-west-1'];
    }

    /**
     * Extend on update email using smtp driver scenario.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $resolver
     *
     * @return void
     */
    protected function extendSmtp(ValidatorResolver $resolver)
    {
        $this->addRequiredForSecretField($resolver, 'email_password', 'enable_change_password');
    }

    /**
     * Extend on update email using mailgun driver scenario.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $resolver
     *
     * @return void
     */
    protected function extendMailgun(ValidatorResolver $resolver)
    {
        $this->addRequiredForSecretField($resolver, 'email_secret', 'enable_change_secret');
    }

    /**
     * Extend on update email using mandrill driver scenario.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $resolver
     *
     * @return void
     */
    protected function extendMandrill(ValidatorResolver $resolver)
    {
        $this->addRequiredForSecretField($resolver, 'email_secret', 'enable_change_secret');
    }

    /**
     * Extend on update email using SES driver scenario.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $resolver
     *
     * @return void
     */
    protected function extendSes(ValidatorResolver $resolver)
    {
        $this->addRequiredForSecretField($resolver, 'email_secret', 'enable_change_secret');
    }

    /**
     * Add required for secret or password field.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $resolver
     * @param  string  $field
     * @param  string  $hidden
     *
     * @return void
     */
    protected function addRequiredForSecretField(ValidatorResolver $resolver, $field, $hidden)
    {
        $resolver->sometimes($field, 'required', function ($input) use ($hidden) {
            return ($input->$hidden == 'yes');
        });
    }

}
