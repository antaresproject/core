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

namespace Antares\Notifier\Events;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Str;

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

        if ($message->getContentType() === 'text/html' ||
                ($message->getContentType() === 'multipart/alternative' && $message->getBody())
        ) {
            $message->setBody($converter->convert($message->getBody()));
        }

        foreach ($message->getChildren() as $part) {
            if (Str::contains($part->getContentType(), 'text/html')) {
                $part->setBody($converter->convert($part->getBody()));
            }
        }
    }

}
