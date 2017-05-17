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


namespace Antares\Notifier\Plugins;

use Swift_Events_SendEvent;
use Swift_Events_SendListener;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class CssInliner implements Swift_Events_SendListener
{

    /**
     * @param  \Swift_Events_SendEvent  $evt
     *
     * @return void
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();

        $converter = new CssToInlineStyles();
        $converter->setEncoding($message->getCharset());
        $converter->setUseInlineStylesBlock();
        $converter->setCleanup();

        if ($message->getContentType() === 'text/html' ||
                ($message->getContentType() === 'multipart/alternative' && $message->getBody())
        ) {
            $converter->setHTML($message->getBody());
            $message->setBody($converter->convert());
        }

        foreach ($message->getChildren() as $part) {
            if (strpos($part->getContentType(), 'text/html') === 0) {
                $converter->setHTML($part->getBody());
                $part->setBody($converter->convert());
            }
        }
    }

    /**
     * Do nothing.
     *
     * @param Swift_Events_SendEvent $evt
     *
     * @return void
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        //
    }

}
