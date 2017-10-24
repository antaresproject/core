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

class MenuCreated {

    /**
     * @var Menu
     */
    public $menu;

    /**
     * MenuCreated constructor.
     * @param Menu $menu
     */
    public function __construct(Menu $menu) {
        $this->menu = $menu;
    }

}