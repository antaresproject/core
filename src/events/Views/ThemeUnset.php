<?php

namespace Antares\Events\Views;

use Antares\Foundation\Events\AbstractEvent;

class ThemeUnset extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Theme unset';

    /** @var string */
    protected static $description = 'Runs when the theme is unset';

    /** @var string */
    public $themeName;

    /**
     * ThemeUnset constructor
     *
     * @param string|null $themeName
     */
    public function __construct(string $themeName = null)
    {
        $this->themeName = $themeName;

        parent::__construct();
    }

}
