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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Widget;

use Closure;
use Antares\Support\Manager;
use Antares\Widget\Handlers\Menu;
use Antares\Widget\Handlers\Pane;
use Antares\Widget\Handlers\Placeholder;

class WidgetManager extends Manager
{

    /**
     * {@inheritdoc}
     */
    protected $blacklisted = [];

    /**
     * Create Menu driver.
     *
     * @param  string  $name
     *
     * @return \Antares\Widget\Handlers\Menu
     */
    protected function createMenuDriver($name)
    {
        $config = $this->app->make('config')->get("antares/widget::menu.{$name}", []);

        return new Menu($name, $config);
    }

    /**
     * Create Pane driver.
     *
     * @param  string  $name
     *
     * @return \Antares\Widget\Handlers\Pane
     */
    protected function createPaneDriver($name)
    {
        $config = $this->app->make('config')->get("antares/widget::pane.{$name}", []);
        return new Pane($name, $config);
    }

    /**
     * Create Placeholder driver.
     *
     * @param  string  $name
     *
     * @return \Antares\Widget\Handlers\Placeholder
     */
    protected function createPlaceholderDriver($name)
    {
        $config = $this->app->make('config')->get("antares/widget::placeholder.{$name}", []);
        return new Placeholder($name, $config);
    }

    /**
     * Get the default driver.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app->make('config')->get('antares/widget::driver', 'placeholder.default');
    }

    /**
     * Set the default driver.
     *
     * @param  string  $name
     *
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app->make('config')->set('antares/widget::driver', $name);
    }

    /**
     * Get the selected driver and extend it via callback.
     *
     * @param  string  $name
     * @param  \Closure|null  $callback
     *
     * @return \Antares\Widget\Handler
     */
    public function of($name, Closure $callback = null)
    {
        if ($name instanceof Closure) {
            $callback = $name;
            $name     = $this->getDefaultDriver();
        }

        $instance = $this->make($name);

        if ($instance instanceof Handler && !is_null($callback)) {
            call_user_func($callback, $instance);
        }

        return $instance;
    }

}
