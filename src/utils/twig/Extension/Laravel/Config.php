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

use TwigBridge\Extension\Laravel\Config as SupportConfig;

/**
 * Access Laravels form builder in your Twig templates.
 */
class Config extends SupportConfig
{

    /**
     * Create a new config extension
     *
     * @param \Illuminate\Config\Repository
     */
    public function __construct($config = null)
    {
        $this->config = $config;
    }

}
