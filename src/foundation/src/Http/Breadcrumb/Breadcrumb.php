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

namespace Antares\Foundation\Http\Breadcrumb;

use DaveJamesMiller\Breadcrumbs\Facade as Breadcrumbs;

class Breadcrumb
{

    /**
     * When shows security section
     */
    public function onSecurity()
    {
        Breadcrumbs::register('security', function($breadcrumbs) {
            $breadcrumbs->push('Security', handles('antares/foundation::settings/security'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('security'));
    }

    /**
     * when shows mail configuration form
     */
    public function onMailConfiguration()
    {
        Breadcrumbs::register('mail_configuration', function($breadcrumbs) {
            $breadcrumbs->push('Mail configuration', handles('antares/foundation::settings/mail'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('mail_configuration'));
    }

    /**
     * when shows components list
     */
    public function onComponentsList()
    {
        if (!Breadcrumbs::exists('modules')) {
            Breadcrumbs::register('modules', function($breadcrumbs) {
                $breadcrumbs->push('Modules', handles('antares::modules'));
            });
        }
        view()->share('breadcrumbs', Breadcrumbs::render('modules'));
    }

    /**
     * when shows component configuration form
     *
     * @param String $component
     */
    public function onComponentConfigure($component)
    {
        $this->onComponentsList();
        Breadcrumbs::register('component-configure', function($breadcrumbs) use($component) {
            $breadcrumbs->parent('components');
            $breadcrumbs->push('Configuration: ' . $component->getFullName());
        });
        view()->share('breadcrumbs', Breadcrumbs::render('component-configure'));
    }

    /**
     * on settings
     */
    public function onSettings()
    {
        Breadcrumbs::register('general-system', function($breadcrumbs) {
            $breadcrumbs->push('General configuration', handles('antares::settings/index'));
        });

        view()->share('breadcrumbs', Breadcrumbs::render('general-system'));
    }

}
