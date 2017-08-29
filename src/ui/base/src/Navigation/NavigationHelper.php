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
 * @package    UI
 * @version    0.9.2
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\Navigation;

use Antares\UI\Navigation\Breadcrumbs\Manager;
use App;

class NavigationHelper {

    /**
     * @return Factory
     */
    public static function factory() : Factory {
        return App::make(Factory::class);
    }

    /**
     * @return Manager
     */
    public static function breadcrumbs() : Manager {
        return App::make(Manager::class);
    }

}
