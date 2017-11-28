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

use Antares\Foundation\Events\AbstractEvent;

class ThemeSet extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Theme set';

    /** @var string */
    protected static $description = 'Runs when the theme is set';

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
