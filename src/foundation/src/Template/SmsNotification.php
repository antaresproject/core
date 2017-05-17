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

namespace Antares\Foundation\Template;

use Antares\View\Notification\AbstractNotificationTemplate;
use Antares\Notifications\Adapter\VariablesAdapter;
use Antares\Notifications\Model\NotificationsStack;

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
        $model = $this->getModel();
        $view  = array_get($model, 'contents.0.content', array_get($model, 'content.0.content'));
        return app(VariablesAdapter::class)->get($view);
    }

    /**
     * Handle notification send result
     * 
     * @return mixed
     */
    public function handle()
    {
        if (empty($this->recipients) && !is_null($recipients = array_get($this->predefinedVariables, 'recipients'))) {
            $this->recipients = $recipients;
        }
        $model = $this->getModel();
        $stack = new NotificationsStack([
            'notification_id' => array_get($model, 'id'),
            'author_id'       => auth()->guest() ? null : user()->id,
            'variables'       => array_merge($this->predefinedVariables, ['recipients' => $this->recipients]),
        ]);
        $stack->save();

        return parent::handle();
    }

}
