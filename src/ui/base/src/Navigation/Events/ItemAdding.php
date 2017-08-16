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

namespace Antares\UI\Navigation\Events;

use Antares\UI\Navigation\Menu;

class ItemAdding {

    /**
     * @var Menu
     */
    public $id;

    /**
     * @var Menu
     */
    public $menu;

    /**
     * ItemAdding constructor.
     * @param string $id
     * @param Menu $menu
     */
    public function __construct(string $id, Menu $menu) {
        $this->id   = $id;
        $this->menu = $menu;
    }

}