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


namespace Antares\Brands\Adapter;

use Illuminate\Contracts\View\Factory as View;

class AbstractAdapter
{

    /**
     * colors array container
     *
     * @var array 
     */
    protected $colors = [];

    /**
     * view instance
     *
     * @var View 
     */
    protected $view;

    /**
     * constructing
     * 
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * colors setter
     * 
     * @param array $colors
     * @return \Antares\Brands\Adapter\FormStyler
     */
    public function with($colors)
    {
        $this->colors = $colors;
        return $this;
    }

    /**
     * default style method
     */
    protected function style()
    {
        
    }

    /**
     * share styles with view
     * 
     * @return \Illuminate\View\View
     */
    public function share()
    {
        return $this->view->share('brandstyles', $this->style());
    }

}
