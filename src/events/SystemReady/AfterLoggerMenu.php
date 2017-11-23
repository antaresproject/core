<?php

namespace Antares\Events\SystemReady;

use Antares\UI\Handler;
use Antares\Foundation\Support\MenuHandler;
use Antares\Foundation\Events\AbstractEvent;

class AfterLoggerMenu extends AfterMenu
{

    /** @var string */
    protected static $name = 'Antares Ready: After logger menu';

    /** @var string */
    protected static $description = 'Runs after logger menu element is added';

    /** @var Handler|MenuHandler */
    public $menu;

    /**
     * AfterLoggerMenu constructor
     *
     * @param Handler|MenuHandler $menu
     */
    public function __construct($menu)
    {
        $this->menu = $menu;
    }

}
