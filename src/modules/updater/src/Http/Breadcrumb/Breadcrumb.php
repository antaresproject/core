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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Http\Breadcrumb;

use DaveJamesMiller\Breadcrumbs\Facade as Breadcrumbs;

class Breadcrumb
{

    /**
     * when shows notifications list
     * 
     * @param type $type
     */
    public function onSystemVersion()
    {
        Breadcrumbs::register('system-version', function($breadcrumbs) {
            $breadcrumbs->push('System version', handles('antares::updater'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('system-version'));
    }

    /**
     * when shows versions list
     */
    public function onVersionsList()
    {
        Breadcrumbs::register('system-versions', function($breadcrumbs) {
            $breadcrumbs->push('Versions', handles('antares::updater'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('system-versions'));
    }

}
