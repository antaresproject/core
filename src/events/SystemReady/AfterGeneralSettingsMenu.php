<?php

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
