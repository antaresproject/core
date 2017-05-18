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


namespace Antares\Twig\Extension;

use Twig_SimpleFunction;
use Twig_Extension;

/**
 * Access Laravels asset class in your Twig templates.
 */
class Resource extends Twig_Extension
{

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Antares_Twig_Extension_Resource';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        $resourceLink = new Twig_SimpleFunction('resource_link', function ($name, $resource) {
            return app()->make('asset.symlinker')->publish($name . '/' . $resource, base_path("src/components/{$name}/public/{$resource}"));
        });
        return [
            $resourceLink
        ];
    }

}
