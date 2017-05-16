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



namespace Antares\Tester\Http\Breadcrumb;

use DaveJamesMiller\Breadcrumbs\Facade as Breadcrumbs;
use function handles;
use function view;

class Breadcrumb
{

    /**
     * when shows module tester form
     */
    public function onForm()
    {
        Breadcrumbs::register('tester', function($breadcrumbs) {
            $breadcrumbs->push('Module Tester', handles('antares::tools/tester'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('tester'));
    }

}
