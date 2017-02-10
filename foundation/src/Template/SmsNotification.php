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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Template;

use Antares\View\Notification\AbstractNotificationTemplate;
use Antares\Notifications\Adapter\VariablesAdapter;

class SmsNotification extends AbstractNotificationTemplate implements SendableNotification
{

    /**
     * type of notification template
     *
     * @var String
     */
    protected $type = 'sms';

    /**
     * notification category
     *
     * @var type 
     */
    protected $category = 'default';

    /**
     * Gets title
     * 
     * @return String
     */
    public function getTitle()
    {
        return array_get($this->getModel(), 'contents.0.title');
    }

    /**
     * renders template
     * 
     * @return String
     */
    public function render($view = null)
    {
        $view = array_get($this->getModel(), 'contents.0.content');
        return app(VariablesAdapter::class)->get($view);
    }

    /**
     * Handle notification send result
     * 
     * @return mixed
     */
    public function handle()
    {
        $result = parent::handle();
        $params = ['variables' => ['recipients' => $this->recipients, 'title' => $this->getTitle()]];
        return (!$result) ? notify('sms.notification_not_sent', $params) : notify('sms.notification_sent', $params);
    }

}
