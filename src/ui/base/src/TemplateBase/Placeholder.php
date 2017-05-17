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
 * @package    UI
 * @version    0.9.2
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\TemplateBase;

use Antares\Support\Str;
use Antares\UI\Handler;
use Closure;

class Placeholder extends Handler
{

    /**
     * {@inheritdoc}
     */
    protected $type = 'placeholder';

    /**
     * {@inheritdoc}
     */
    protected $config = [
        'defaults' => [
            'value'   => '',
            'content' => '',
        ],
        'content'  => '',
    ];

    /**
     * {@inheritdoc}
     */
    public function add($id, $location = '#', $callback = null)
    {
        if (is_string($location) && Str::startsWith($location, '^:')) {
            $location = '#';
        } elseif ($location instanceof Closure) {
            $callback = $location;
            $location = '#';
        }

        $item = $this->nesty->add($id, $location ?: '#');

        if ($callback instanceof Closure) {
            $item->value = $callback;
        }

        return $item;
    }

}
