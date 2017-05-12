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


namespace Antares\Widget\Handlers;

use Antares\Widget\Handler;
use Illuminate\Support\Facades\Event;

class Menu extends Handler
{

    /**
     * {@inheritdoc}
     */
    protected $type = 'menu';

    /**
     * {@inheritdoc}
     */
    protected $config = [
        'defaults' => [
            'attributes' => [],
            'icon'       => '',
            'link'       => '#',
            'title'      => '',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function add($id, $location = '#', $callback = null)
    {
        Event::fire('antares.ready: menu.before.' . $id, $this);
        $return = $this->addItem($id, $location, $callback);
        Event::fire('antares.ready: menu.after.' . $id, $this);
        return $return;
    }

}
