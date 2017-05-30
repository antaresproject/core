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

use TwigBridge\Extension\Loader\Facade\Caller;

class Facades extends Loader
{

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Loader_Facades';
    }

    /**
     * {@inheritDoc}
     */
    public function getGlobals()
    {
        $load    = $this->config->get('twigbridge.extensions.facades', []);
        $globals = [];

        foreach ($load as $facade => $options) {
            list($facade, $callable, $options) = $this->parseCallable($facade, $options);

            $globals[$facade] = new Caller($callable, $options);
        }

        return $globals;
    }

}
