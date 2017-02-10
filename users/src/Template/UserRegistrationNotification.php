<?php

/**
 * Part of the Antares Project package.
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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Template;

use Antares\View\Notification\Notification;

class UserRegistrationNotification extends Notification
{

    /**
     * type of notification template
     *
     * @var String
     */
    protected $type = 'email';

    /**
     * default notification brand
     *
     * @var String 
     */
    protected $brands = ['default'];
    /*
     * notification template title
     * 
     * @var String
     */
    protected $title  = 'Registration';

    /**
     * container with available languages
     *
     * @var array 
     */
    protected $availableLanguages = [
        'en'
    ];

    /**
     * default template language
     *
     * @var String
     */
    protected $defaultLanguage = 'en';

    /**
     * template paths
     *
     * @var array
     */
    protected $templatePaths = [
        'en' => 'antares/foundation::emails.auth.en.register',
    ];

    /**
     * notification events
     *
     * @var array 
     */
    protected $events = [
        'user-register-notification'
    ];

    /**
     * notification tags
     *
     * @var array 
     */
    protected $tags = [
        'register'
    ];

    /**
     * notification category
     *
     * @var type 
     */
    protected $category = 'users';

    /**
     * default recipients
     *
     * @var array 
     */
    protected $recipients = [];

}
