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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Registry;

use Antares\Registry\Registry as BaseRegistry;

class Registry extends BaseRegistry
{

    /**
     * Class name of the singleton registry object.
     * 
     * @var string
     */
    private static $_registryClassName = '\Antares\UI\UIComponents\Registry\Registry';

}
