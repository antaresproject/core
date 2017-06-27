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

use Illuminate\Mail\Message;
use Illuminate\View\View;
use Exception;
use Log;

class NotificationHandler
{

    /**
     * handle notification event
     *
     * @param AbstractNotificationTemplate $instance
     * @return bool
     */
    public function handle(AbstractNotificationTemplate $instance)
    {
        $type     = $instance->getType();
        $notifier = $this->getNotifierAdapter($type);

        if (!$notifier) {
            return false;
        }

        $render         = $instance->render();
        $view           = $render instanceof View ? $render->render() : $render;
        $title          = $instance->getTitle();
        $recipients     = $instance->getRecipients();
        $attachments    = $instance->getAttachments();

        return $notifier->send($view, [], function(Message $m) use($title, $recipients, $attachments) {
            $m->to($recipients);
            $m->subject($title);

            foreach($attachments as $attachment) {
                $m->attach($attachment->getPath(),$attachment->getComputedOptions());
            }
        });
    }

    /**
     * Gets notifier adapter by type.
     *
     * @param string $type
     * @return mixed
     * @throws Exception;
     */
    public function getNotifierAdapter(string $type)
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
