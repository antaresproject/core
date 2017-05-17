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


namespace Antares\Customfield\Twig;

use Twig_SimpleFunction;
use Twig_Extension;

class Customfield extends Twig_Extension
{

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Customfield_Extension';
    }

    /**
     * create widget view helper for get brand title
     * 
     * @return Twig_SimpleFunction
     */
    protected function hasCustomfield()
    {

        $function = function ($object, $name) {
            return method_exists($object, 'hasCustomfield') ? $object->hasCustomfield($name) : false;
        };
        return new Twig_SimpleFunction(
                'hasCustomfield', $function
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            $this->hasCustomfield()
        ];
    }

}
