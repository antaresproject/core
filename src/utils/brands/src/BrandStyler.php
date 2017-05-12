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


namespace Antares\Brands;

use Illuminate\Contracts\Container\Container;
use Antares\Brands\Adapter\LayoutStyler;
use Antares\Brands\Adapter\FormStyler;

class BrandStyler
{

    /**
     * container instance
     *
     * @var Container
     */
    protected $container;

    /**
     * constructing
     * 
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * instance of form style adapter
     * 
     * @param array $colors
     * @return void
     */
    public function formAdapter(array $colors = null)
    {
        return $this->container->make(FormStyler::class)->with($colors);
    }

    /**
     * instance of layout adapter
     * 
     * @param array $colors
     * @return type
     */
    public function layoutAdapter(array $colors = [])
    {
        return $this->container->make(LayoutStyler::class)->with($colors);
    }

}
