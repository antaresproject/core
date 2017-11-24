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
use Antares\Support\Str;
use Illuminate\Support\Facades\Event;
use Antares\Foundation\Support\MenuHandler;
use Antares\Foundation\Events\AbstractEvent;

class AfterMenu extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Ready: After menu';

    /** @var string */
    protected static $description = 'Runs after menu element is added';

    /** @var string */
    public $menuName;

    /** @var Handler|MenuHandler */
    public $menu;

    /**
     * AfterMenu constructor
     *
     * @param string              $menuName
     * @param Handler|MenuHandler $menu
     */
    public function __construct(string $menuName, $menu)
    {
        $this->menuName = $menuName;
        $this->menu = $menu;

        parent::__construct();

        if (class_exists($eventClass = sprintf('Antares\Events\SystemReady\After%sMenu', ucfirst(Str::camel($menuName))))) {
            Event::fire(new $eventClass($menu));
        }
    }

}
