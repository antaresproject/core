<?php

/**
 * Part of the Antares Project package.
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
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */




namespace Antares\Twig\Extension\Laravel;

use Twig_Extension;
use Twig_SimpleFunction;
use Illuminate\Container\Container;

/**
 * Access Laravels url class in your Twig templates.
 */
class Handles extends Twig_Extension
{

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Handles';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                    'handles', function ($name) {
                        $arguments = array_slice(func_get_args(), 1);
                        $app       = Container::getInstance()->make('antares.app');
                        return $app->handles($name, $arguments);
                    }
            ),
        ];
    }

}
