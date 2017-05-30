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

use Illuminate\Contracts\Foundation\Application;
use Twig_SimpleFunction;
use Twig_Extension;

class Extension extends Twig_Extension
{

    /**
     * Application instance
     *
     * @var Application
     */
    protected $application;

    /**
     * constructing
     * 
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Antares_Twig_Extension_Component';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('extension_active', function ($name) {
                        return extension_active($name);
                    })
        ];
    }

}
