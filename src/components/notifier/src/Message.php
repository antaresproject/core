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
 namespace Antares\Notifier;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Antares\Contracts\Notification\Message as MessageContract;

class Message extends Fluent implements MessageContract
{
    /**
     * Create a new Message instance.
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  string|null  $subject
     *
     * @return static
     */
    public static function create($view, array $data = [], $subject = null)
    {
        return new static([
            'view'    => $view,
            'data'    => $data,
            'subject' => $subject,
        ]);
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        return Arr::get($this->attributes, 'data', []);
    }

    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return Arr::get($this->attributes, 'subject', '');
    }

    /**
     * Get view.
     *
     * @return string|array
     */
    public function getView()
    {
        return Arr::get($this->attributes, 'view');
    }
}
