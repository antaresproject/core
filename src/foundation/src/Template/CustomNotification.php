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
use Antares\Notifications\Model\NotificationsStackParams;
use Antares\Notifications\Adapter\VariablesAdapter;
use Antares\Notifications\Model\NotificationsStack;
use Illuminate\Database\Eloquent\Model;
use App\User as BaseUser;
use Antares\Model\User;

class CustomNotification extends AbstractNotificationTemplate
{

    /**
     * type of notification template
     *
     * @var String
     */
    protected $type = 'email';

    /**
     * notification events
     *
     * @var array 
     */
    protected $events = [
        'antares.notifier.events.custom'
    ];

    /**
     * Gets title
     * 
     * @return String
     */
    public function getTitle()
    {
        $title = array_get($this->getModel(), 'contents.0.subject');

        if( empty($title) ) {
            $title = array_get($this->getModel(), 'contents.0.title');
        }

        return $this->getVariablesAdapter()
            ->setVariables((array) $this->predefinedVariables)->get($title);
    }

    /**
     * renders template
     * 
     * @return String
     */
    public function render($view = null)
    {
        $view = array_get($this->getModel(), 'contents.0.content');

        return app(VariablesAdapter::class)->fill($view);
    }

    /**
     * handle queue event
     * 
     * @return void
     */
    public function handle()
    {
        $model = $this->getModel();
        $stack = new NotificationsStack([
            'notification_id' => array_get($model, 'id'),
            'author_id'       => auth()->guest() ? null : user()->id,
            'variables'       => $this->predefinedVariables,
        ]);

        if ($stack->save()) {
            $params = $this->params();
            foreach ($params as $id) {
                $stackParams = new NotificationsStackParams(['stack_id' => $stack->id, 'model_id' => $id]);
                $stack->params()->save($stackParams);
            }
        }

        return;
    }

    /**
     * Extract params from backtrace
     * 
     * @return array
     */
    protected function params()
    {
        $params = array_get(debug_backtrace(0, 3), '2.args.1');
        $return = [];
        foreach ($params as $model) {
            if (!$model instanceof Model) {
                continue;
            }
            $modelId = ($model instanceof User or $model instanceof BaseUser) ? $model->id : ( array_key_exists('user_id', $model->getAttributes()) ? $model->user_id : null );
            if (is_null($modelId)) {
                continue;
            }
            $return[] = $modelId;
        }
        return $return;
    }

}
