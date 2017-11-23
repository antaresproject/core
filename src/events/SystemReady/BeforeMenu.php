<?php

namespace Antares\Events\SystemReady;

use Antares\UI\Handler;
use Antares\Foundation\Support\MenuHandler;
use Antares\Foundation\Events\AbstractEvent;

class BeforeMenu extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Ready: Before menu';

    /** @var string */
    protected static $description = 'Runs before menu element is added';

    /** @var string */
    public $menuName;

    /** @var Handler|MenuHandler */
    public $menu;

    /**
     * BeforeMenu constructor
     *
     * @param string              $menuName
     * @param Handler|MenuHandler $menu
     */
    public function __construct(string $menuName, $menu)
    {
        $this->menuName = $menuName;
        $this->menu = $menu;

        parent::__construct();
    }

}
