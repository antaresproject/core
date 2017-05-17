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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\View\Helper;

use Illuminate\Contracts\Foundation\Application;

abstract class AbstractHelper
{

    /**
     * application instance
     *
     * @var Application 
     */
    protected $app;

    /**
     * attributes container
     *
     * @var array 
     */
    protected $attributes = [
        'default' => false
    ];

    /**
     * helper view path
     *
     * @var String 
     */
    protected $view = 'view-helpers::filter-select';

    /**
     * constructing
     * 
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * attributes setter
     * 
     * @param array $attributes
     * @return FilterDropdown
     */
    public function setAttributes(array $attributes = null)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * renders view helper
     */
    public abstract function render();
}
