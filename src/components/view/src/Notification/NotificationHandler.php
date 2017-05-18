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

namespace Antares\View\Notification;

use Antares\View\Notification\AbstractNotificationTemplate;
use Antares\Support\Facades\Foundation;
use Illuminate\View\View;
use Exception;

class NotificationHandler
{

    /**
     * handle notification event 
     * 
     * @param Notification $instance
     */
    public function handle(AbstractNotificationTemplate $instance)
    {
        $type     = $instance->getType();
        $notifier = $this->getNotifierAdapter($type);

        if (!$notifier) {
            return false;
        }
        $render     = $instance->render();
        $view       = $render instanceof View ? $render->render() : $render;
        $title      = $instance->getTitle();
        $recipients = $instance->getRecipients();
        return $notifier->send($view, [], function($m) use($title, $recipients) {
                    $m->to($recipients->pluck('email')->toArray());
                    $m->subject($title);
                });
    }

    /**
     * gets notifier adapter by type
     * 
     * @param String $type
     * @return boolean
     * @throws Exception
     */
    public function getNotifierAdapter($type)
    {
        try {
            $config         = config("antares/notifier::{$type}");
            $notifierConfig = $config['adapters'][$config['adapters']['default']];
            if (!class_exists($notifierConfig['model'])) {
                throw new Exception(sprintf('Notifier adapter: %s not exists.', $notifierConfig['model']));
            }
            if (in_array($type, ['email', 'mail'])) {
                return Foundation::make('antares.notifier.email');
            }
            return Foundation::make("antares.notifier.{$type}");
        } catch (Exception $ex) {
            return false;
        }
    }

}
