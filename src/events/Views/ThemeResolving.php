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
