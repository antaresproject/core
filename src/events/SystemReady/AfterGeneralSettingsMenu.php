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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Events\SystemReady;

use Antares\UI\Handler;
use Antares\Foundation\Support\MenuHandler;
use Antares\Foundation\Events\AbstractEvent;

class AfterGeneralSettingsMenu extends AfterMenu
{

    /** @var string */
    protected static $name = 'Antares Ready: After general settings menu';

    /** @var string */
    protected static $description = 'Runs after general settings menu element is added';

    /** @var Handler|MenuHandler */
    public $menu;

    /**
     * AfterGeneralSettingsMenu constructor
     *
     * @param Handler|MenuHandler $menu
     */
    public function __construct($menu)
    {
        $this->menu = $menu;
    }

}
