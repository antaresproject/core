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

namespace Antares\Users\Processor\Account;

use Antares\Contracts\Foundation\Listener\Account\ProfileDashboard as Listener;
use Antares\Contracts\Foundation\Command\Account\ProfileDashboard as Command;
use Antares\Breadcrumb\Facade as Breadcrumbs;
use Antares\Foundation\Processor\Processor;
use Antares\UI\WidgetManager;

class ProfileDashboard extends Processor implements Command
{

    /**
     * The widget manager implementation.
     *
     * @var \Antares\Widget\WidgetManager
     */
    protected $widget;

    /**
     * Construct a new User Dashboard processor.
     *
     * @param \Antares\Widget\WidgetManager $widget
     */
    public function __construct(WidgetManager $widget)
    {
        $this->widget = $widget;
    }

    /**
     * View dashboard.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\ProfileDashboard  $listener
     *
     * @return mixed
     */
    public function show(Listener $listener)
    {
        Breadcrumbs::register('dashboard', function($breadcrumbs) {
            $breadcrumbs->push('Dashboard', handles('antares::foundation/'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('dashboard'));
        return $listener->showDashboard();
    }

}
