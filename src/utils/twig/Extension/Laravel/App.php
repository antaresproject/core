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
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Twig\Extension\Laravel;

use Twig_Extension;
use Twig_SimpleFunction;
use Illuminate\Container\Container;

/**
 * Access Laravels url class in your Twig templates.
 */
class App extends Twig_Extension
{

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_App';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                    'app', function ($name) {
                        $arguments = array_slice(func_get_args(), 1);
                        if (is_null($name)) {
                            return Container::getInstance();
                        }
                        return Container::getInstance()->make($name, $arguments);
                    }
            ),
        ];
    }

}
