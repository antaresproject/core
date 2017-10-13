<?php

namespace Antares\Events\Views;

use Antares\Foundation\Events\AbstractEvent;

class ThemeBoot extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Themeun boot';

    /** @var string */
    protected static $description = 'Runs when the theme is loaded';

    /** @var string */
    public $themeName;

    /**
     * ThemeBoot constructor
     *
     * @param string $themeName
     */
    public function __construct(string $themeName)
    {
        $this->themeName = $themeName;

        parent::__construct();
    }

}
