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

namespace Antares\Updater\Composers;

use Illuminate\Contracts\Foundation\Application;
use Antares\UI\WidgetManager;

class SandboxPlaceholder
{

    /**
     * The widget manager implementation.
     *
     * @var \Antares\UI\WidgetManager
     */
    protected $widget;

    /**
     * application instance
     *
     * @var Application 
     */
    protected $app;

    /**
     * Construct a new composer.
     *
     * @param  \Antares\UI\WidgetManager  $widget
     */
    public function __construct(WidgetManager $widget, Application $app)
    {
        $this->widget = $widget;
        $this->app    = $app;
    }

    /**
     * Handle pane for dashboard page.
     *
     * @return void
     */
    public function compose()
    {
        $sandbox = app('request')->get('sandbox');
        if (!$sandbox) {
            return;
        }
        $url = null;
        if (preg_match('/^([0-9]).([0-9]).([0-9])$/', $sandbox)) {
            publish('updater', ['js/update.js', 'js/sandbox.js']);
            $url = handles('antares::updater/production/iterations', ['csrf' => true]);
        }
        $pane = $this->widget->make('placeholder.sandbox');
        $pane->add('sandbox')
                ->attributes(['class' => 'six columns widget'])
                ->content(view('antares/foundation::updater.partials._sandbox', ['sandbox' => $sandbox, 'url' => $url]));
    }

}
