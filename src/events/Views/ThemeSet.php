<?php

namespace Antares\Events\Views;

use Antares\Foundation\Events\AbstractEvent;

class ThemeSet extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Themeun set';

    /** @var string */
    protected static $description = 'Runs when the theme is unset';

    /** @var string */
    public $themeName;

    /**
     * ThemeSet constructor
     *
     * @param string $themeName
     */
    public function __construct(string $themeName)
    {
        $this->themeName = $themeName;

        parent::__construct();
    }

}
