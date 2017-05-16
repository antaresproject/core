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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Logger\Twig;

use Twig_Extension;
use Twig_SimpleFunction;

class Logger extends Twig_Extension
{

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Logger_Extension_Logger';
    }

    /**
     * create widget view helper
     * 
     * @return Twig_SimpleFunction
     */
    protected function logStyler()
    {

        $function = function () {

            return app(\Arcanedev\LogViewer\Contracts\Utilities\LogStyler::class);
        };
        return new Twig_SimpleFunction(
                'log_styler', $function
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            $this->logStyler()
        ];
    }

}
