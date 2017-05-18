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




namespace Antares\Twig\Extension\Loader;

use Twig_SimpleFilter;

class Filters extends Loader
{

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Loader_Filters';
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        $load    = $this->config->get('twigbridge.extensions.filters', []);
        $filters = [];

        foreach ($load as $method => $callable) {
            list($method, $callable, $options) = $this->parseCallable($method, $callable);

            $filter = new Twig_SimpleFilter(
                    $method, function () use ($callable) {
                return call_user_func_array($callable, func_get_args());
            }, $options
            );

            $filters[] = $filter;
        }

        return $filters;
    }

}
