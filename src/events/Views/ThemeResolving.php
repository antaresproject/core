<?php

namespace Antares\Events\Views;

use Antares\Contracts\Theme\Theme;
use Antares\Foundation\Events\AbstractEvent;
use Illuminate\Contracts\Container\Container;

class ThemeResolving extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Theme resolving';

    /** @var string */
    protected static $description = 'Runs when the theme is resolving';

    /** @var Theme */
    public $theme;

    /** @var Container */
    public $app;

    /**
     * ThemeResolving constructor
     *
     * @param Theme     $theme
     * @param Container $app
     */
    public function __construct(Theme $theme, Container $app)
    {
        $this->theme = $theme;
        $this->app = $app;

        parent::__construct();
    }

}
