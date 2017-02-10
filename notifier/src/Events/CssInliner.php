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


namespace Antares\Notifier\Events;

use Illuminate\Support\Str;
use Illuminate\Mail\Events\MessageSending;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class CssInliner
{

    /**
     * Handle converting to inline CSS.
     *
     * @param  \Illuminate\Mail\Events\MessageSending  $sending
     *
     * @return void
     */
    public function handle(MessageSending $sending)
    {
        $message = $sending->message;

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
            if (Str::contains($part->getContentType(), 'text/html')) {
                $converter->setHTML($part->getBody());
                $part->setBody($converter->convert());
            }
        }
    }

}
