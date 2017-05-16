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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */





namespace Antares\Tester\Contracts;

use Illuminate\View\View;

interface TesterProcess
{

    /**
     * runs module configuration tests
     * 
     * @return \Illuminate\Http\Response
     */
    public function run();

    /**
     * runs module configuration tests
     * 
     * @param Illuminate\View\View $view
     * @return \Illuminate\Http\Response
     */
    public function render(View $view);
}
