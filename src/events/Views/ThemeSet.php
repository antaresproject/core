<?php

namespace Antares\Events\Views;

use Antares\Foundation\Events\AbstractEvent;

class ThemeSet extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Theme set';

    /** @var string */
    protected static $description = 'Runs when the theme is unset';

    /** @var string */
    public $themeName;

    /**
     * ThemeSet constructor
     *
     * @param string|null $themeName
     */
    public function __construct(string $themeName = null)
    {
        $this->themeName = $themeName;

        parent::__construct();
    }

}
