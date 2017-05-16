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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Control\Contracts;

use Antares\Html\Form\Grid;
use Illuminate\Support\Fluent;

interface ControlsAdapter
{

    /**
     * adaptee controls on form instance
     * 
     * @param \Antares\Html\Form\Grid $grid
     * @param array $controls
     * @param Fluent $model
     */
    public function adaptee(Grid &$grid, array $controls = array(), Fluent $model);
}
