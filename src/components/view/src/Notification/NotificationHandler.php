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

use Antares\Notifier\Adapter\AbstractAdapter;
use Illuminate\View\View;
use Exception;
use Log;

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
            $m->to($recipients);
            $m->subject($title);
        });
    }

    /**
     * Gets notifier adapter by type.
     *
     * @param string $type
     * @return AbstractAdapter
     * @throws Exception;
     */
    public function getNotifierAdapter(string $type) : AbstractAdapter
    {
        try {
            $config = config("antares/notifier::{$type}");
            $notifierConfig = $config['adapters'][$config['adapters']['default']];

            if (!class_exists($notifierConfig['model'])) {
                throw new Exception(sprintf('Notifier adapter: %s not exists.', $notifierConfig['model']));
            }

            if (in_array($type, ['email', 'mail'], true)) {
                return app()->make('antares.notifier.email');
            }

            return app()->make("antares.notifier.{$type}");
        } catch (Exception $ex) {
            Log::emergency($ex);

            throw $ex;
        }
    }

}
